<?php

class BitcoinPayment implements PaymentStrategy {
    public function pay(float $amount): void {
        echo "Paid $$amount using Bitcoin.\n";
    }
}
