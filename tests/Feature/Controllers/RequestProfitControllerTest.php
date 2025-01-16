<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\ProfitTracked;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class RequestProfitControllerTest extends TestCase
{
    public function testRequestProfit_WithoutTypeAndValidParams_ShouldReturnProfit(): void
    {
        $payload = [
            'rule' => ProfitTracked::RULE_GREATER,
            'billions' => '100',
        ];
        $response = $this->postJson('/api/crawler', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['company_name', 'profit', 'rank']
            ]);
    }

    public function testRequestProfit_WithActiveTypeAndValidParams_ShouldReturnActive(): void
    {
        $payload = [
            'rule' => ProfitTracked::RULE_GREATER,
            'billions' => '100',
            'type' => 'active',
        ];
        $response = $this->postJson('/api/crawler', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['company_name', 'profit', 'rank']
            ]);
    }

    public function testRequestProfit_WhenWebsiteIsOffline_ShouldReturnEmptyResponse(): void
    {
        Http::fake([
            'https://pt.wikipedia.org/wiki/Lista_das_maiores_empresas_do_Brasil' => Http::response([], 503),
        ]);
        $payload = [
            'rule' => ProfitTracked::RULE_GREATER,
            'billions' => '100',
        ];
        $response = $this->postJson('/api/crawler', $payload);
        $response->assertStatus(200)
            ->assertJson([]);
    }

    public function testRequestProfit_WhenNoDataMatchesFilters_ShouldReturnEmptyResponse(): void
    {
        $payload = [
            'rule' => ProfitTracked::RULE_GREATER,
            'billions' => '999999999999999999999999999',
        ];
        $response = $this->postJson('/api/crawler', $payload);
        $response->assertStatus(200)
            ->assertJson([]);
    }

    public function testRequestProfit_WithEmptyPayload_ShouldReturnValidationErrors(): void
    {
        $response = $this->postJson('/api/crawler', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rule', 'billions']);
    }

    public function testRequestProfit_WhenCacheIsUsed_ShouldReturnCachedResponse(): void
    {
        $payload = [
            'rule' => ProfitTracked::RULE_GREATER,
            'billions' => '100',
        ];
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn('<html><body>mocked html</body></html>');
        $response = $this->postJson('/api/crawler', $payload);
        $response->assertStatus(200)
            ->assertJson([]);
    }

    public static function getErroScenarios(): \Generator
    {
        yield 'missing rule' => [
            'payload' => [
                'billions' => "50",
            ],
            'missing_field' => ['rule'],
            'expected_message' => [
                'rule' => [
                    'O campo rule é obrigatório'
                ]
            ]
        ];
        yield 'missing billions' => [
            'payload' => [
                'rule' => ProfitTracked::RULE_GREATER,
            ],
            'missing_field' => ['billions'],
            'expected_message' => [
                'billions' => [
                    'O campo billions é obrigatório'
                ]
            ]
        ];
        yield 'missing range' => [
            'payload' => [
                'rule' => ProfitTracked::RULE_BETWEEN,
                'billions' => "50",
            ],
            'missing_field' => ['range'],
            'expected_message' => [
                'range' => [
                    'O campo range é obrigatório quando a rule for ' . ProfitTracked::RULE_BETWEEN
                ]
            ]
        ];
        yield 'invalid rule' => [
            'payload' => [
                'rule' => 'INVALID_RULE',
                'billions' => "50",
            ],
            'missing_field' => ['rule'],
            'expected_message' => [
                'rule' => [
                    'O campo rule é inválido'
                ]
            ]
        ];
        yield 'billions not numeric' => [
            'payload' => [
                'rule' => ProfitTracked::RULE_GREATER,
                'billions' => "not_a_number",
            ],
            'missing_field' => ['billions'],
            'expected_message' => [
                'billions' => [
                    'O campo billions deve ser um número'
                ]
            ]
        ];
        yield 'range invalid format' => [
            'payload' => [
                'rule' => ProfitTracked::RULE_BETWEEN,
                'billions' => "50",
                'range' => [50],
            ],
            'missing_field' => ['range'],
            'expected_message' => [
                'range' => [
                    'O campo range deve conter exatamente 2 valores.'
                ]
            ]
        ];
        yield 'range not numeric' => [
            'payload' => [
                'rule' => ProfitTracked::RULE_BETWEEN,
                'billions' => "50",
                'range' => ['a', 'b'],
            ],
            'missing_field' => ['range'],
            'expected_message' => [
                'range' => [
                    'Ambos os valores em range devem ser numéricos.'
                ]
            ]
        ];
        yield 'range values order incorrect' => [
            'payload' => [
                'rule' => ProfitTracked::RULE_BETWEEN,
                'billions' => "50",
                'range' => [100, 50],
            ],
            'missing_field' => ['range'],
            'expected_message' => [
                'range' => [
                    'O primeiro valor do range deve ser menor que o segundo.'
                ]
            ]
        ];
        yield 'with invalid type' => [
            'payload' => [
                'rule' => ProfitTracked::RULE_GREATER,
                'billions' => "50",
                'type' => 'error',
            ],
            'missing_field' => ['type'],
            'expected_message' => [
                'type' => [
                    'O campo type é inválido'
                ]
            ]
        ];
    }

    #[DataProvider('getErroScenarios')]
    public function testRequestProfit_WithInvalidParams_ShouldCry(
        array $payload,
        array $missing_field,
        array $expected_message
    ): void {
        $response = $this->postJson('/api/crawler', $payload);
        $response->assertStatus(422)
            ->assertJsonValidationErrors($missing_field)
            ->assertJson([
                'errors' => $expected_message
            ]);
    }

    public function testRequestProfit_WhenCrawlerReturnsValidData_ShouldReturnValidResponse(): void
    {
        $this->mockCrawlerResponse(['key' => 'valid_value']);
        $payload = [
            'rule' => ProfitTracked::RULE_GREATER,
            'billions' => '100',
        ];
        $response = $this->postJson('/api/crawler', $payload);
        $response->assertStatus(200)
            ->assertJson([]);
    }

    private function mockCrawlerResponse(string|array $data): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($data);
    }
}
