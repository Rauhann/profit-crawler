<?php

declare(strict_types=1);

namespace App\Services;

use App\Dtos\AbstractDto;
use App\Dtos\SaveCrawlerRequestDto;
use App\Helpers\NumberHelper;
use App\Models\CrawlerRequest;
use App\Models\ProfitTracked;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

abstract class ProfitService
{
    abstract protected function buildCrawlerData(Crawler $crawler): array;

    abstract function profitField(): string;

    abstract protected function getSourceResponseKeys(array $crawlerData): array;

    final public function execute(
        string $source,
        array $filters = []
    ): array {
        $crawler = $this->executeCrawler($source);
        $crawlerData = $crawler['crawler_data'];
        if (is_null($crawlerData?->html())) {
            return [];
        }
        $crawlerData = $this->buildCrawlerData($crawlerData);
        $crawlerDataFiltered = $this->filterCrawlerData(
            crawlerData: $crawlerData,
            rule: $filters['rule'],
            billions: $filters['billions'],
            range: $filters['range'] ?? []
        );
        return $this->buildResponse(
            crawlerData: $crawlerData,
            crawlerDataFiltered: $crawlerDataFiltered,
            crawlerRequestId: $crawler['crawler_request_id'],
            saveResponse: $crawler['save_result']
        );
    }

    protected function executeCrawler(string $source): array
    {
        $save = false;
        $crawlerRequestId = null;
        try {
            $html = Cache::remember($source, 600, function () use ($source, &$save, &$crawlerRequestId) {
                $crawlerService = new CrawlerService();
                $crawler = $crawlerService->request('GET', $source);
                $html = $crawler?->html();
                $crawlerRequestId = $this->saveCrawlerRequest($source, $html)?->id;
                $save = true;
                return $html;
            });
        } catch (Throwable $exception) {
            Log::error("Error execute crawler on $source " . $exception->getMessage());
            $html = null;
        }
        return [
            'crawler_data' => new Crawler($html),
            'save_result' => $save,
            'crawler_request_id' => $crawlerRequestId,
        ];
    }

    protected function saveCrawlerRequest(
        string $source,
        ?string $crawler
    ): CrawlerRequest {
        $dto = $this->buildDto([
            'source' => $source,
            'content' => $crawler,
        ]);
        return CrawlerRequest::create($dto->toArray());
    }

    protected function buildDto(array $data): AbstractDto
    {
        return new SaveCrawlerRequestDto([
            'source' => $data['source'],
            'content' => $data['content'],
        ]);
    }

    protected function filterCrawlerData(
        array $crawlerData,
        string $rule,
        string $billions,
        array $range = []
    ): array {
        return array_filter($crawlerData, function ($item) use ($rule, $billions, $range) {
            $field = $this->profitField();
            $profit = $this->convertToBillions($item[$field]);
            return match ($rule) {
                ProfitTracked::RULE_GREATER => $profit > $billions,
                ProfitTracked::RULE_SMALLER => $profit < $billions,
                ProfitTracked::RULE_BETWEEN => $profit >= $range[0] && $profit <= $range[1],
                default => false
            };
        });
    }

    protected function convertToBillions(string $profit): float
    {
        if (strpos($profit, 'milhões') !== false) {
            return NumberHelper::millionsToBillions($profit);
        }
        return NumberHelper::stringToFloat($profit);
    }

    protected function buildResponse(
        array $crawlerData,
        array $crawlerDataFiltered,
        ?int $crawlerRequestId,
        bool $saveResponse
    ): array {
        if ($saveResponse) {
            $crawlerData = $this->getSourceResponseKeys($crawlerData);
            $allResponse = $this->sanitizeData($crawlerData);
            $this->saveProfitTracked($crawlerRequestId, $allResponse);
        }
        $crawlerDataFiltered = $this->getSourceResponseKeys($crawlerDataFiltered);
        return $this->sanitizeData($crawlerDataFiltered);
    }

    protected function sanitizeData(array $data): array
    {
        $response = [];
        foreach ($data as $item) {
            $profit = $this->convertToBillions($item['profit']);
            $data = [
                'company_name' => $item['company_name'],
                'profit' => $profit,
                'rank' => (int) $item['rank'],
            ];
            $response[] = $data;
        }
        return $response;
    }

    protected function saveProfitTracked(
        int $crawlerRequestId,
        array $data
    ): void {
        foreach ($data as $item) {
            $item['crawler_request_id'] = $crawlerRequestId;
            (new ProfitTrackedService())->saveProfitTracked($item);
        }
    }
}
