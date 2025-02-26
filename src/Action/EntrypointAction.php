<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatform\Action;

use ApiPlatform\Api\Entrypoint;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Generates the API entrypoint.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class EntrypointAction
{
    public function __construct(
        private readonly ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
        private readonly ?ProviderInterface $provider = null,
        private readonly ?ProcessorInterface $processor = null
    ) {
    }

    /**
     * @return Entrypoint|Response
     */
    public function __invoke(Request $request = null)
    {
        if ($this->provider && $this->processor) {
            $context = ['request' => $request];
            $operation = new Get(class: Entrypoint::class, provider: fn () => new Entrypoint($this->resourceNameCollectionFactory->create()));
            $body = $this->provider->provide($operation, [], $context);

            return $this->processor->process($body, $operation, [], $context);
        }

        return new Entrypoint($this->resourceNameCollectionFactory->create());
    }
}
