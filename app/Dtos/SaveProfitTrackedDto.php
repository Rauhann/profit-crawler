<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Dtos\AbstractDto;

class SaveProfitTrackedDto extends AbstractDto
{
    protected int $crawler_request_id;
    protected string $company_name;
    protected float $profit;
    protected int $rank;

    public function __construct(array $params)
    {
        $this->crawler_request_id = $params['crawler_request_id'];
        $this->company_name = $params['company_name'];
        $this->profit = $params['profit'];
        $this->rank = $params['rank'];
    }

    public function getCrawlerRequestId(): int
    {
        return $this->crawler_request_id;
    }

    public function getCompanyName(): string
    {
        return $this->company_name;
    }

    public function getProfit(): float
    {
        return $this->profit;
    }

    public function getRank(): int
    {
        return $this->rank;
    }
}
