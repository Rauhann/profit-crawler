<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;

class WikipediaSourceService extends ProfitService
{
    public function buildCrawlerData(Crawler $crawler): array
    {
        $tableData = [];
        $table = $crawler->filter('table.wikitable')->first();
        $headers = $table->filter('th')->each(function ($node) {
            return trim($node->text());
        });
        $table->filter('tr')->each(function ($row, $index) use (&$tableData, $headers) {
            $rowData = $row->filter('td')->each(function ($cell) {
                return trim($cell->text());
            });
            if (count($rowData) === count($headers)) {
                $tableData[] = array_combine($headers, $rowData);
            }
        });
        return $tableData;
    }

    public function profitField(): string
    {
        return "Lucro(em bilhões de US$)";
    }

    public function getSourceResponseKeys(array $crawlerData): array
    {
        $response = [];
        foreach ($crawlerData as $item) {
            $response[] = [
                'company_name' => $item['Nome'],
                'profit' => $item[$this->profitField()],
                'rank' => $item['ForbesGlobal 2000'],
            ];
        }
        return $response;
    }
}
