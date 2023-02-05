<?php

declare(strict_types=1);

namespace Yiisoft\Aliases\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;

final class ConfigTest extends TestCase
{
    public function testBase(): void
    {
        $container = $this->createContainer();

        $aliases = $container->get(Aliases::class);

        $this->assertInstanceOf(Aliases::class, $aliases);
        $this->assertSame([], $aliases->getAll());
    }

    public function testOverrideParams(): void
    {
        $container = $this->createContainer([
            'yiisoft/aliases' => [
                'aliases' => [
                    '@root' => __DIR__,
                ],
            ],
        ]);

        $aliases = $container->get(Aliases::class);

        $this->assertInstanceOf(Aliases::class, $aliases);
        $this->assertSame(['@root' => __DIR__], $aliases->getAll());
    }

    private function createContainer(?array $params = null): Container
    {
        return new Container(
            ContainerConfig::create()->withDefinitions(
                $this->getDiConfig($params)
            )
        );
    }

    private function getDiConfig(?array $params = null): array
    {
        if ($params === null) {
            $params = $this->getParams();
        }
        return require dirname(__DIR__) . '/config/di.php';
    }

    private function getParams(): array
    {
        return require dirname(__DIR__) . '/config/params.php';
    }
}
