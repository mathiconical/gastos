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
                $products[$currentProduct]['unit'] = explode(': ', $fieldValue)[1];
            }

            if ($idx % $this->total_column_per_item === 3) {
                $changedFieldValue = str_replace(',', '.', $fieldValue);
                $products[$currentProduct]['price'] = str_replace('Valor total R$: R$ ', '', $changedFieldValue);
            }
        });

        return $products;
    }
}
