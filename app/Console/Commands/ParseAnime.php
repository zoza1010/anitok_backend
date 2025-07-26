<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Anime\Anime;
use App\Models\Anime\Genre;
use App\Models\Anime\AnimeStatus;
use App\Models\Anime\AnimeType;
use App\Models\Anime\AnimeAgeRating;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ParseAnime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Добавлен параметр count - сколько аниме парсить.
     *
     * @var string
     */
    protected $signature = 'app:parse-anime {count=50 : Количество аниме для парсинга}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсим топ аниме с Shikimori API и сохраняем в БД';

    /**
     * Очищает описание от любых тегов в квадратных скобках (например, [i], [character=...], [[текст]], и т.п.)
     */
    private function cleanDescription($desc) {
        // Удалить все теги в квадратных скобках, включая вложенные и двойные
        return preg_replace('/\[.*?\]/u', '', $desc);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $perPage = 50; // Максимальное количество на странице
        $pages = ceil($count / $perPage);
        $totalParsed = 0;

        $client = new Client([
            'headers' => [
                'User-Agent' => 'AniTokParser/1.0',
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
        ]);

        $this->info("Начинаем парсинг топ {$count} аниме...");

        for ($page = 1; $page <= $pages; $page++) {
            $limit = ($page == $pages) ? ($count - ($pages - 1) * $perPage) : $perPage;
            
            if ($limit <= 0) break;

            $this->info("Загружаем страницу {$page} (аниме: {$limit})...");

            // Получаем топ аниме с пагинацией
            $response = $client->get('https://shikimori.one/api/animes', [
                'query' => [
                    'order' => 'ranked',
                    'limit' => $limit,
                    'page' => $page,
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->error("Ошибка получения списка аниме: HTTP {$response->getStatusCode()}");
                return;
            }

            $animes = json_decode($response->getBody(), true);

            foreach ($animes as $index => $anime) {
                $animeId = $anime['id'];
                $this->info("Парсим аниме #".($totalParsed + 1)." - ID: {$animeId}");

                // Обработка с учётом 429 и повтором с задержкой
                $attempts = 0;
                do {
                    $attempts++;
                    $animeResponse = $client->get("https://shikimori.one/api/animes/{$animeId}");
                    $status = $animeResponse->getStatusCode();

                    if ($status === 429) {
                        $this->warn("Получен 429 Too Many Requests. Ждем 10 секунд и повторяем...");
                        sleep(10);
                    } elseif ($status !== 200) {
                        $this->error("Ошибка при запросе аниме ID {$animeId}: HTTP {$status}");
                        continue 2; // переходим к следующему аниме
                    }
                } while ($status === 429 && $attempts < 3);

                $animeData = json_decode($animeResponse->getBody(), true);

                // --- Genres ---
                $genres = [];
                if (!empty($animeData['genres'])) {
                    foreach ($animeData['genres'] as $genre) {
                        $g = Genre::updateOrCreate([
                            'name_eng' => $genre['name'],
                        ], [
                            'name' => $genre['russian'] ?? $genre['name'],
                        ]);
                        $genres[] = $g->id;
                    }
                }

                // --- Status ---
                if (!empty($animeData['status'])) {
                    $statusMap = [
                        'anons' => 'Анонс',
                        'ongoing' => 'Онгоинг',
                        'released' => 'Вышло',
                    ];
                    $nameEng = $animeData['status'];
                    $name = $statusMap[$nameEng] ?? $nameEng;

                    $status = AnimeStatus::updateOrCreate([
                        'name_eng' => $nameEng,
                    ], [
                        'name' => $name,
                    ]);
                } else {
                    $status = null;
                }

                // --- Type ---
                if (!empty($animeData['kind'])) {
                    $typeMap = [
                        'tv' => 'ТВ',
                        'movie' => 'Фильм',
                        'ova' => 'OVA',
                        'ona' => 'ONA',
                        'special' => 'Спешл',
                        'music' => 'Музыка',
                    ];
                    $nameEng = $animeData['kind'];
                    $name = $typeMap[$nameEng] ?? $nameEng;

                    $type = AnimeType::updateOrCreate([
                        'name_eng' => $nameEng,
                    ], [
                        'name' => $name,
                    ]);
                } else {
                    $type = null;
                }

                // --- Age Rating ---
                if (!empty($animeData['rating'])) {
                    $ratingMap = [
                        'g' => '0+',
                        'pg' => '6+',
                        'pg_13' => '13+',
                        'r' => '17+',
                        'r_plus' => '18+',
                        'rx' => '18+ (эротика)',
                    ];
                    $nameEng = $animeData['rating'];
                    $name = $ratingMap[$nameEng] ?? $nameEng;

                    $ageRating = AnimeAgeRating::updateOrCreate([
                        'name_eng' => $nameEng,
                    ], [
                        'name' => $name,
                    ]);
                } else {
                    $ageRating = null;
                }

                // --- Сохраняем само аниме ---
                $animeModel = Anime::updateOrCreate([
                    'id' => $animeData['id'],
                ], [
                    'title' => $animeData['russian'] ?? $animeData['name'] ?? null,
                    'description' => isset($animeData['description']) ? $this->cleanDescription($animeData['description']) : null,
                    'poster_url' => $animeData['image']['original'] ?? null,
                    'aired_on' => $animeData['aired_on'] ?? null,
                    'released_on' => $animeData['released_on'] ?? null,
                    'next_episode_at' => $animeData['next_episode_at'] ?? null,
                    'duration' => $animeData['duration'] ?? null,
                    'episodes' => $animeData['episodes'] ?? null,
                    'episodes_aired' => $animeData['episodes_aired'] ?? null,
                    'age_rating_id' => $ageRating ? $ageRating->id : null,
                    'status_id' => $status ? $status->id : null,
                    'type_id' => $type ? $type->id : null,
                ]);

                // Связи с жанрами
                if (count($genres) > 0) {
                    $animeModel->genres()->sync($genres);
                }

                $totalParsed++;

                // Ждем 0.25 секунды чтобы не превышать 5 запросов в секунду
                usleep(250000);
                
                // Если достигли нужного количества, выходим
                if ($totalParsed >= $count) {
                    break 2;
                }
            }
        }

        $this->info("Парсинг завершён успешно! Обработано {$totalParsed} аниме.");
    }
}