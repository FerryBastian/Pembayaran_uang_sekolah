<?php

namespace Tests\Unit;

use App\Services\WhatsappService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WhatsappServiceTest extends TestCase
{
    #[DataProvider('phoneNumbers')]
    public function test_it_formats_phone_numbers(string $input, string $expected): void
    {
        $service = new WhatsappService;

        $this->assertSame($expected, $service->formatPhone($input));
    }

    public static function phoneNumbers(): array
    {
        return [
            'local number' => ['0812-3456-7890', '6281234567890'],
            'international number' => ['+62 812 3456 7890', '6281234567890'],
            'formatted number' => ['6281234567890', '6281234567890'],
        ];
    }

    public function test_it_returns_true_for_successful_gowa_response(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"code":"SUCCESS"}')]);
        $client = new Client([
            'base_uri' => 'http://gowa.test/',
            'handler' => HandlerStack::create($mock),
        ]);

        $service = new WhatsappService($client);

        $this->assertTrue($service->sendMessage('081234567890', 'Pesan test'));
    }

    public function test_it_returns_false_when_gowa_request_fails(): void
    {
        $mock = new MockHandler([new Response(500, [], '{"code":"ERROR"}')]);
        $client = new Client([
            'base_uri' => 'http://gowa.test/',
            'handler' => HandlerStack::create($mock),
            'http_errors' => true,
        ]);

        $service = new WhatsappService($client);

        $this->assertFalse($service->sendMessage('081234567890', 'Pesan test'));
    }
}
