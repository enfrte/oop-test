<?php 

interface Course {
    public function getName(): string;
    public function getPrice(): float;
}

class MathCourse implements Course {
    public function getName(): string {
        return "Math Course";
    }

    public function getPrice(): float {
        return 100.0;
    }
}

class ScienceCourse implements Course {
    public function getName(): string {
        return "Science Course";
    }

    public function getPrice(): float {
        return 120.0;
    }
}

class CourseShop {
    private Course $course;
    private ?Discount $discount = null;

    public function __construct(Course $course) {
        $this->course = $course;
    }

    public function applyDiscount(Discount $discount): void {
        $this->discount = $discount;
        echo "Applied " . $discount->getDescription() . " to " . $this->course->getName() . "<br>";
    }

    public function checkout(): void {
        $price = $this->course->getPrice();
        if ($this->discount) {
            $price = $this->discount->applyDiscount($price);
        }

        echo "Checking out " . $this->course->getName() . " for $" . number_format($price, 2) . "<br>";
    }

    public function getPrice(): float {
        return $this->discount ? $this->discount->applyDiscount($this->course->getPrice()) : $this->course->getPrice();
    }

    public function getCourse(): Course {
        return $this->course;
    }
}


class CourseShopFactory {
    public static function create(Course $course): CourseShop {
        return new CourseShop($course);
    }
}

class OrderManager {
    private array $cart = []; // Stores CourseShop instances
    private ?Discount $orderDiscount = null;

    public function addToCart(Course $course): void {
        $shop = CourseShopFactory::create($course);
        $this->cart[] = $shop;
        echo $course->getName() . " added to cart.<br>";
    }

    public function applyCourseDiscount(string $courseName, Discount $discount): void {
        foreach ($this->cart as $shop) {
            if ($shop->getCourse()->getName() === $courseName) {
                $shop->applyDiscount($discount);
                return;
            }
        }
        echo "Course not found in cart.<br>";
    }

    public function applyOrderDiscount(Discount $discount): void {
        $this->orderDiscount = $discount;
        echo "Applied order-wide " . $discount->getDescription() . "<br>";
    }

    public function checkout(): void {
        if (empty($this->cart)) {
            echo "Cart is empty. Nothing to checkout.<br>";
            return;
        }

        echo "Processing order:<br>";
        $total = 0;
        foreach ($this->cart as $shop) {
            $shop->checkout();
            $total += $shop->getPrice();
        }

        if ($this->orderDiscount) {
            $total = $this->orderDiscount->applyDiscount($total);
        }

        echo "Final Total: $" . number_format($total, 2) . "<br>";
        $this->cart = []; // Clear cart after checkout
    }
}



interface Discount {
    public function applyDiscount(float $price): float;
    public function getDescription(): string;
}

// Fixed Amount Discount (e.g., $20 off)
class FixedDiscount implements Discount {
    private float $amount;

    public function __construct(float $amount) {
        $this->amount = $amount;
    }

    public function applyDiscount(float $price): float {
        return max(0, $price - $this->amount); // Ensure price doesn't go negative
    }

    public function getDescription(): string {
        return "$" . $this->amount . " off";
    }
}

// Percentage Discount (e.g., 10% off)
class PercentageDiscount implements Discount {
    private float $percentage;

    public function __construct(float $percentage) {
        $this->percentage = $percentage;
    }

    public function applyDiscount(float $price): float {
        return $price * (1 - $this->percentage / 100);
    }

    public function getDescription(): string {
        return $this->percentage . "% off";
    }
}


// EXAMPLE

$orderManager = new OrderManager();

// User selects courses
$orderManager->addToCart(new MathCourse());
$orderManager->addToCart(new ScienceCourse());

// Apply a discount to the Math Course
$orderManager->applyCourseDiscount("Math Course", new FixedDiscount(20));

// Apply a 10% order-wide discount
$orderManager->applyOrderDiscount(new PercentageDiscount(10));

// Checkout
$orderManager->checkout();
