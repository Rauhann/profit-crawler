<?php

declare(strict_types=1);

namespace App\Services;

use App\Dtos\SaveProfitTrackedDto;
use App\Models\ProfitTracked;

class ProfitTrackedService
{
    public function saveProfitTracked(array $data): ProfitTracked
    {
        $dto = new SaveProfitTrackedDto($data);
        return ProfitTracked::create($dto->toArray());
    }
}
