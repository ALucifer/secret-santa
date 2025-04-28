<?php

namespace App\Services\Request\Resolver;

use App\Services\Request\Attribute\PrefixPagination;
use App\Services\Request\DTO\PaginationDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class PaginationResolver implements ValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === PaginationDTO::class;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable<PaginationDTO>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }

        /** @var PrefixPagination $prefixPagination */
        $prefixPagination = $argument->getAttributes(PrefixPagination::class)[0];
        $page = sprintf('%spage', $prefixPagination->prefix);

        yield new PaginationDTO(
                $request->query->getInt($page, 1),
                $prefixPagination->limit,
                $page
            );
    }
}