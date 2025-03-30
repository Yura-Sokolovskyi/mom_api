<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\OrderStatus;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(type: 'string')]
    private string $customerEmail;

    #[ORM\Column(type: 'string', enumType: OrderStatus::class)]
    private OrderStatus $status;

    #[ORM\Column(type: 'float')]
    private float $totalPrice;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['persist'], orphanRemoval: true)]
    private Collection $items;

    public function __construct(string $customerEmail)
    {
        $this->id = Uuid::v4();
        $this->customerEmail = $customerEmail;
        $this->status = OrderStatus::NEW;
        $this->createdAt = new \DateTimeImmutable();
        $this->items = new ArrayCollection();
        $this->totalPrice = 0;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): void
    {
        $this->status = $status;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): void
    {
        $this->items->add($item);
        $item->setOrder($this);
    }

    public function calculateTotal(): void
    {
        $this->totalPrice = array_reduce(
            $this->items->toArray(),
            fn($sum, OrderItem $item) => $sum + ($item->getUnitPrice() * $item->getQuantity()),
            0
        );
    }
}
