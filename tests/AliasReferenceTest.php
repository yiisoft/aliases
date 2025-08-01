<?php

declare(strict_types=1);

namespace Yiisoft\Aliases\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Aliases\AliasReference;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Test\Support\Container\SimpleContainer;

use function PHPUnit\Framework\assertSame;

final class AliasReferenceTest extends TestCase
{
    #[TestWith(['/path/to/app', '@app'])]
    #[TestWith(['/path/to/app/runtime/test', '@runtime/test'])]
    #[TestWith(['/test/path', '/test/path'])]
    public function testResolve(string $expected, string $alias): void
    {
        $container = new SimpleContainer([
            Aliases::class => new Aliases([
                '@app' => '/path/to/app',
                '@runtime' => '@app/runtime'
            ]),
        ]);

        $reference = AliasReference::to($alias);

        assertSame($expected, $reference->resolve($container));
    }

    public static function dataInvalidValue(): array
    {
        return [
            'int' => [123],
            'null' => [null],
            'array' => [['@app']],
            'object' => [new stdClass()],
            'float' => [12.3],
            'boolean' => [true],
        ];
    }

    #[DataProvider('dataInvalidValue')]
    public function testInvalidValue(mixed $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Alias must be a string.');
        AliasReference::to($value);
    }
}
