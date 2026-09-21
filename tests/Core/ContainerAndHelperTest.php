<?php

declare(strict_types=1);

namespace Tests\Core;

use App\Core\Container;
use App\Core\Helper;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ContainerTest extends TestCase
{
    public function testGetReturnsSharedInstance(): void
    {
        $container = new Container();
        $calls = 0;
        $container->set('service', static function () use (&$calls): object {
            $calls++;
            return new \stdClass();
        });

        $first = $container->get('service');
        $second = $container->get('service');

        self::assertSame($first, $second);
        self::assertSame(1, $calls);
    }

    public function testHasReportsRegisteredServices(): void
    {
        $container = new Container();
        $container->set('cfg', static fn (): array => ['ok' => true]);

        self::assertTrue($container->has('cfg'));
        self::assertFalse($container->has('missing'));
    }

    public function testGetThrowsForUnknownService(): void
    {
        $container = new Container();

        $this->expectException(RuntimeException::class);
        $container->get('missing');
    }
}

final class HelperTest extends TestCase
{
    public function testEscapeEncodesHtml(): void
    {
        self::assertSame('&lt;b&gt;x&lt;/b&gt;', Helper::e('<b>x</b>'));
    }

    public function testUrlBuildsPageQuery(): void
    {
        self::assertSame('index.php?page=reports', Helper::url('reports'));
    }
}
