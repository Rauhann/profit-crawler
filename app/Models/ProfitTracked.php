<?php

declare(strict_types=1);

namespace App\Models;

class ProfitTracked extends BaseModel
{
    public const RULE_GREATER = 'greater';
    public const RULE_SMALLER = 'smaller';
    public const RULE_BETWEEN = 'between';

    protected $table = 'profits_tracked';

    protected $fillable = [
        'uuid',
        'crawler_request_id',
        'company_name',
        'profit',
        'rank',
    ];

    public static function getAllRules(): array
    {
        return [
            self::RULE_GREATER,
            self::RULE_SMALLER,
            self::RULE_BETWEEN,
        ];
    }
}
