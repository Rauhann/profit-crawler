<?php

declare(strict_types=1);

namespace App\Enums;

use App\Services\ProfitService;
use App\Services\WikipediaSourceService;

enum ProfitDataSourceEnum: string
{
    case WIKIPEDIA = 'wikipedia';

    public function getSourceClass(): ProfitService
    {
        return match ($this) {
            self::WIKIPEDIA => new WikipediaSourceService(),
        };
    }

    public function getWebSite(): string
    {
        return match ($this) {
            self::WIKIPEDIA => 'https://pt.wikipedia.org/wiki/Lista_das_maiores_empresas_do_Brasil',
        };
    }
}
