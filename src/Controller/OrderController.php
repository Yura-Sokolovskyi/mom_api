<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\CreateOrderRequest;
use App\Enum\OrderResponseMessages;
use App\Enum\OrderStatus;
use App\Service\OrderService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[Route('/api')]
#[OA\Tag(name: 'Orders')]
class OrderController extends AbstractController
{
    #[Route('/orders', name: 'create_order', methods: ['POST'])]
    #[OA\Post(
        summary: 'Create order',
        requestBody: new OA\RequestBody(
            request: CreateOrderRequest::class,
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: CreateOrderRequest::class)
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Order created',
                content: new OA\JsonContent(
                    example: [
                        'id' => 'f190c201-a79c-4b41-8edd-987488ea02c7',
                        'status' => 'NEW'
                    ]
                )
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: OrderResponseMessages::INVALID_INPUT->value
            )
        ]
    )]
    public function create(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        OrderService $orderService
    ): JsonResponse {
        $dto = $serializer->deserialize(
            $request->getContent(),
            CreateOrderRequest::class,
            'json'
        );

        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $order = $orderService->createOrder($dto);

        return $this->json(['id' => $order->getId(), 'status' => $order->getStatus()]);
    }

    #[OA\Get(
        summary: 'Retrieve order by ID',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Order found',
                content: new OA\JsonContent(
                    example: [
                        'id' => 'f190c201-a79c-4b41-8edd-987488ea02c7',
                        'customer_email' => 'user@example.com',
                        'status' => OrderStatus::NEW->value,
                        'total_price' => 91.2,
                        'created_at' => '2025-03-29T16:31:04+00:00',
                        'items' => [
                            [
                                'product_name' => 'Keyboard',
                                'unit_price' => 45.6,
                                'quantity' => 2,
                            ]
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: OrderResponseMessages::NOT_FOUND->value
            )
        ]
    )]
    #[Route('/orders/{id}', name: 'get_order', methods: ['GET'])]
    public function get(string $id, OrderService $orderService): JsonResponse
    {
        $order = $orderService->getOrder($id);
        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }
        return $this->json($orderService->formatOrder($order));
    }

    #[OA\Get(
        summary: 'List all orders',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'List of orders',
                content: new OA\JsonContent(
                    example: [
                        [
                            'id' => 'f190c201-a79c-4b41-8edd-987488ea02c7',
                            'customer_email' => 'user@example.com',
                            'status' => 'NEW',
                            'total_price' => 91.2,
                            'created_at' => '2025-03-29T16:31:04+00:00',
                            'items' => [
                                [
                                    'product_name' => 'Keyboard',
                                    'unit_price' => 45.6,
                                    'quantity' => 2,
                                ]
                            ]
                        ]
                    ]
                )
            )
        ]
    )]
    #[Route('/orders', name: 'list_orders', methods: ['GET'])]
    public function list(OrderService $orderService): JsonResponse
    {
        return $this->json($orderService->listOrders());
    }

    #[OA\Get(
        summary: 'Order status',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Get order status',
                content: new OA\JsonContent(
                    example: ['status' => OrderStatus::NEW->value]
                )
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: OrderResponseMessages::NOT_FOUND->value
            )
        ]
    )]
    #[Route('/orders/{id}/status', name: 'order_status', methods: ['GET'])]
    public function status(string $id, OrderService $orderService): JsonResponse
    {
        $order = $orderService->getOrder($id);

        if (!$order) {
            return $this->json(['error' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['status' => $order->getStatus()]);
    }
}
