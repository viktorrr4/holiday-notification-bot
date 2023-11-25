<?php

namespace App\Services\Holidays;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

class Handler
{
    //
    public static function getHolidays(): array
    {
        session()->forget('holidays');

        if(session()->has('holidays')) {
            return session()->get('holidays');
        }

        $client = new Client();
        $day = Carbon::create(now()->year, now()->month, now()->day)->format('Y-m-d');
        $url = "https://www.calend.ru/day/$day/";

        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.1234.567 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'
        ];

        $options = [
            'headers' => $headers
        ];

        try {
            $response = $client->request('GET', $url, $options);
            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                $html = $response->getBody()->getContents();

                // Создаем объект Crawler для парсинга HTML
                $crawler = new Crawler($html);

                $date = $crawler->filter('h1.day_title')->innerText();

                $holidayList = $crawler->filter('.index_page > .holidays > .itemsNet')->children();

                $data = [];
                foreach ($holidayList as $holiday) {
                    $holiday = new Crawler($holiday);
                    $title = $holiday->filter('.title > a')->text();
                    $data[] = [
                        'name' => $title,
                        'text' => $holiday->filter('.descr')->text(),
                    ];
                }

                $response = [
                    'success' => true,
                    'data' => [
                        'date' => $date,
                        'holidays' => $data
                    ]
                ];

                session()->put('holidays', $response);

                return $response;
            } else {
                return [
                    'success' => false,
                    'data' => [
                        'message' => "Страница не была получена. Код ответа: $statusCode"
                    ]
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'data' => [
                    'message' => "Произошла ошибка: " . $e->getMessage()
                ]
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'data' => [
                    'message' => "Произошла ошибка запроса: " . $e->getMessage()
                ]
            ];
        }
    }
}
