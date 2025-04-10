<?php

namespace App\Services;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;

class ProcessSefazPageService
{
    private int $total_column_per_item;

    public function __construct()
    {
        $this->total_column_per_item = 4;
    }

    public function get(string $url): array
    {
        $crawler = $this->request('GET', $url);

        return $this->processTableToArray($crawler);
    }

    public function post(string $url): array
    {
        $crawler = $this->request('POST', $url);

        return $this->processTableToArray($crawler);
    }

    private static function request(string $method, string $url): Crawler
    {
        $client = new HttpBrowser();
        $crawler = $client->request($method, $url);

        return $crawler;
    }

    private function processTableToArray(Crawler $crawler): array
    {
        $tableOfItems = $crawler->filter('#myTable td');

        $products = [];
        $currentProduct = -1;

        $tableOfItems->each(function ($el, $idx) use (&$products, &$currentProduct) {
            $fieldValue = preg_replace('/\s\s+/', '', ($el->getNode(0)->nodeValue));

            if ($idx % $this->total_column_per_item === 0) {
                $currentProduct += 1;
                $products[$currentProduct]['name'] = explode('(', $fieldValue)[0];
                $products[$currentProduct]['name'] = trim(str_replace(' KG', '', $products[$currentProduct]['name']));
            }

            if ($idx % $this->total_column_per_item === 1) {
                $products[$currentProduct]['amount'] = str_replace('Qtde total de ítens: ', '', $fieldValue);
            }

            if ($idx % $this->total_column_per_item === 2) {
                $products[$currentProduct]['unit'] = strtoupper(explode(': ', $fieldValue)[1]);
            }

            if ($idx % $this->total_column_per_item === 3) {
                $changedFieldValue = str_replace(',', '.', $fieldValue);
                $products[$currentProduct]['price'] = str_replace('Valor total R$: R$ ', '', $changedFieldValue);
            }
        });

        $datetime_regex_pattern = '/^([0-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/\d{4} ([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/';

        $date = $crawler
            ->filter('table.table.table-hover tbody tr td:nth-child(4)')
            ->each(function (Crawler $node) use ($datetime_regex_pattern) {
                $fullDateTime = trim($node->text());

                if (preg_match($datetime_regex_pattern, $fullDateTime)) {
                    return explode(' ', $fullDateTime)[0];
                } else {
                    return false;
                }
            });

        $products['date'] = array_values(array_filter($date, fn($item): ?string => $item !== false))[0];

        return $products;
    }
}
