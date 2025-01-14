<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Dtos\AbstractDto;

class SaveCrawlerRequestDto extends AbstractDto
{
    protected string $source;
    protected ?string $content;

    public function __construct(array $params)
    {
        $this->source = $params['source'];
        $this->content = $params['content'] ?? null;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
}
