<?php

declare(strict_types=1);

namespace Yiisoft\Aliases;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Yiisoft\Definitions\Contract\ReferenceInterface;

use function is_string;

final class AliasReference implements ReferenceInterface
{
    private string $alias;

    private function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public static function to(mixed $id): self
    {
        if (!is_string($id)) {
            throw new InvalidArgumentException('Alias must be a string.');
        }
        return new self($id);
    }

    public function resolve(ContainerInterface $container): string
    {
        /** @var Aliases $aliases */
        $aliases = $container->get(Aliases::class);
        return $aliases->get($this->alias);
    }
}
