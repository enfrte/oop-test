<?php

class CreditCardPayment implements PaymentStrategy {
    public function pay(float $amount): void {
        echo "Paid $$amount using Credit Card.\n";
    }
}

