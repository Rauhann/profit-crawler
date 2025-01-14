<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ProfitDataSourceEnum;
use App\Http\Requests\RequestProfitRequest;
use Illuminate\Http\JsonResponse;
use Throwable;

class RequestProfitController extends Controller
{
    public function __invoke(RequestProfitRequest $request): JsonResponse
    {
        try {
            $dataSource = $this->defineDataSource();
            $result = $dataSource->getSourceClass()->execute($dataSource->getWebSite(), $request->all());
            return $this->toJson($result, 200);
        } catch (Throwable $exception) {
            return $this->toJson([], $exception->getMessage(), $exception->getCode());
        }
    }

    private function defineDataSource(): ProfitDataSourceEnum
    {
        return ProfitDataSourceEnum::WIKIPEDIA;
    }
}
