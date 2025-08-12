<?php

declare(strict_types=1);

namespace Yiisoft\Aliases;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Yiisoft\Definitions\Contract\ReferenceInterface;

use function is_string;

/**
 * Reference to an alias that will be resolved at runtime.
 */
final class AliasReference implements ReferenceInterface
{
    /**
     * @var string The alias to be resolved.
     */
    private string $alias;

    /**
     * @param string $alias The alias to be resolved.
     */
    private function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    /**
     * Creates a new alias reference.
     *
     * @param string $id The alias to be resolved.
     *
     * @return self An instance of reference.
     *
     * @psalm-suppress MoreSpecificImplementedParamType, DocblockTypeContradiction
     */
    public static function to(mixed $id): self
    {
        if (!is_string($id)) {
            throw new InvalidArgumentException('Alias must be a string.');
        }
        return new self($id);
    }

    /**
     * Retrieves {@see Aliases} from the container and uses it to resolve the alias to its actual path.
     *
     * @param ContainerInterface $container The DI container.
     *
     * @return string The resolved path for the alias.
     */
    public function resolve(ContainerInterface $container): string
    {
        /** @var Aliases $aliases */
        $aliases = $container->get(Aliases::class);
        return $aliases->get($this->alias);
    }
}
