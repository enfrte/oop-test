<?php

class PayPalPayment implements PaymentStrategy {
    public function pay(float $amount): void {
        echo "Paid $$amount using PayPal.\n";
    }
}
