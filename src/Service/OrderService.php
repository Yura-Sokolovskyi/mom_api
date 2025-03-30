<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\CreateOrderRequest;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class OrderService
{
    public function __construct(private EntityManagerInterface $em, private OrderRepository $repo) {}

    public function createOrder(CreateOrderRequest $dto): Order
    {
        $order = new Order($dto->customerEmail);

        foreach ($dto->items as $item) {
            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            $orderItem->setProductName($item['product_name']);
            $orderItem->setUnitPrice($item['unit_price']);
            $orderItem->setQuantity($item['quantity']);
            $order->addItem($orderItem);
        }

        $order->calculateTotal();
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    public function getOrder(string $id): ?Order
    {
        return $this->repo->find($id);
    }

    public function listOrders(): array
    {
        return array_map(fn($o) => $this->formatOrder($o), $this->repo->findAll());
    }

    public function formatOrder(Order $order): array
    {
        return [
            'id' => $order->getId(),
            'customer_email' => $order->getCustomerEmail(),
            'status' => $order->getStatus()->value,
            'total_price' => $order->getTotalPrice(),
            'created_at' => $order->getCreatedAt()->format('c'),
            'items' => array_map(fn($i) => [
                'product_name' => $i->getProductName(),
                'unit_price' => $i->getUnitPrice(),
                'quantity' => $i->getQuantity()
            ], $order->getItems()->toArray())
        ];
    }
}
