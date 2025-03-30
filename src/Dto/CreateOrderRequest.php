<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'CreateOrderRequest', description: 'Request payload for creating an order')]
class CreateOrderRequest
{
    #[Assert\Email]
    #[Assert\NotBlank]
    #[OA\Property(description: 'Customer email address')]
    public string $customerEmail;

    #[Assert\NotBlank]
    #[Assert\Count(min: 1)]
    #[OA\Property(
        description: 'Array of order items',
        type: 'array',
        items: new OA\Items(
            required: ['product_name', 'unit_price', 'quantity'],
            properties: [
                new OA\Property(property: 'product_name', type: 'string'),
                new OA\Property(property: 'unit_price', type: 'number', format: 'float'),
                new OA\Property(property: 'quantity', type: 'integer'),
            ],
            type: 'object'
        )
    )]
    public array $items;
}
