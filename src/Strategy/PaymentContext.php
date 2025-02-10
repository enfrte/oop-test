<?php

class PaymentContext {
    private PaymentStrategy $strategy;

    public function __construct(PaymentStrategy $strategy) {
        $this->strategy = $strategy;
    }

    public function setStrategy(PaymentStrategy $strategy): void {
        $this->strategy = $strategy;
    }

    public function executePayment(float $amount): void {
        $this->strategy->pay($amount);
    }
}
