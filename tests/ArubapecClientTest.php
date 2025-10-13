<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Tests;

use PHPUnit\Framework\TestCase;
use Shellrent\Arubapec\ArubapecClient;

class ArubapecClientTest extends TestCase
{
    public function testClientCanBeInstantiated(): void
    {
        $client = new ArubapecClient();

        self::assertInstanceOf(ArubapecClient::class, $client);
    }
}
