<?php

class CorrelationIdGenerator {
    public static function generate(): string {
        return bin2hex(random_bytes(16)); // Unique 32-character ID
    }
}

// --------------- ORDER CLASS ----------------
class Order {
    private string $correlationId;
    private int $userId;
    private array $items = [];
    private string $status = 'pending';

    public function __construct(int $userId) {
        $this->correlationId = CorrelationIdGenerator::generate();
        $this->userId = $userId;
    }

    public function addItem(Product $product, int $quantity): void {
        $this->items[] = ['product' => $product, 'quantity' => $quantity];
    }

    public function getTotal(): float {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }

    public function getCorrelationId(): string {
        return $this->correlationId;
    }

    public function processOrder(Database $db): void {
        $db->insert("INSERT INTO orders (correlation_id, user_id, status) VALUES (?, ?, ?)", [
            $this->correlationId, $this->userId, $this->status
        ]);
    }
}

// --------------- PRODUCT CLASS ----------------
abstract class Product {
    protected string $name;
    protected float $price;
    protected FulfillmentStrategy $fulfillment;

    public function __construct(string $name, float $price, FulfillmentStrategy $fulfillment) {
        $this->name = $name;
        $this->price = $price;
        $this->fulfillment = $fulfillment;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function fulfill(string $correlationId, Database $db, array $deliveryDetails): void {
        $this->fulfillment->fulfill($correlationId, $this, $db, $deliveryDetails);
    }
}

// --------------- PRODUCT TYPES ----------------
class PhysicalProduct extends Product {
    public function __construct(string $name, float $price) {
        parent::__construct($name, $price, new PhysicalFulfillment());
    }
}

class DigitalProduct extends Product {
    public function __construct(string $name, float $price) {
        parent::__construct($name, $price, new DigitalFulfillment());
    }
}

// --------------- FULFILLMENT STRATEGY INTERFACE ----------------
interface FulfillmentStrategy {
    public function fulfill(string $correlationId, Product $product, Database $db, array $deliveryDetails): void;
}

// --------------- PHYSICAL FULFILLMENT ----------------
class PhysicalFulfillment implements FulfillmentStrategy {
    public function fulfill(string $correlationId, Product $product, Database $db, array $deliveryDetails): void {
        $db->insert("INSERT INTO shipping (correlation_id, product_name, address, status) VALUES (?, ?, ?, 'pending')", [
            $correlationId, $product->name, $deliveryDetails['address']
        ]);
    }
}

// --------------- DIGITAL FULFILLMENT ----------------
class DigitalFulfillment implements FulfillmentStrategy {
    public function fulfill(string $correlationId, Product $product, Database $db, array $deliveryDetails): void {
        $db->insert("INSERT INTO digital_delivery (correlation_id, product_name, email, status) VALUES (?, ?, ?, 'pending')", [
            $correlationId, $product->name, $deliveryDetails['email']
        ]);
    }
}

// --------------- PAYMENT CLASS ----------------
class Payment {
    public static function process(string $correlationId, float $amount, Database $db): void {
        $db->insert("INSERT INTO payments (correlation_id, amount, status) VALUES (?, ?, 'processing')", [
            $correlationId, $amount
        ]);
    }
}

// --------------- DATABASE CLASS ----------------
class Database {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function insert(string $query, array $params): void {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
    }
}

// --------------- USAGE EXAMPLE ----------------
$pdo = new PDO('sqlite::memory:'); // Replace with real DB connection
$db = new Database($pdo);

$userId = 1;
$order = new Order($userId);

$lamp = new PhysicalProduct("Desk Lamp", 50.00);
$ebook = new DigitalProduct("Programming eBook", 15.00);

$order->addItem($lamp, 1);
$order->addItem($ebook, 1);

$order->processOrder($db);

// Process payment
Payment::process($order->getCorrelationId(), $order->getTotal(), $db);

// Fulfill products separately
$lamp->fulfill($order->getCorrelationId(), $db, ['address' => '123 Street']);
$ebook->fulfill($order->getCorrelationId(), $db, ['email' => 'user@example.com']);

echo "Order processed with correlation ID: " . $order->getCorrelationId();
