<?php

declare(strict_types=1);

namespace App\Models;

class CrawlerRequest extends BaseModel
{
    protected $table = 'crawler_requests';

    protected $fillable = [
        'uuid',
        'source',
        'content',
    ];
}
