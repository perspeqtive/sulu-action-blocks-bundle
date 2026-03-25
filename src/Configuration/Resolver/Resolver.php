<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Resolver;

readonly class Resolver implements ResolverInterface
{

    /**
     * @param iterable<TypeResolverInterface> $resolvers
     */
    public function __construct(private iterable $resolvers) {}

    public function resolve(array $data): array {
        $type = $data['type'] ?? 'none';
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($type) === false) {
                continue;
            }
            return $resolver->resolve($data);
        }
        return $data;
    }

}