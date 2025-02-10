<?php

// Create different payment strategies
$paypal = new PayPalPayment();
$creditCard = new CreditCardPayment();
$bitcoin = new BitcoinPayment();

// Context with PayPal
$context = new PaymentContext($paypal);
$context->executePayment(100.50);

$context->setStrategy($creditCard);
$context->executePayment(250.75);

$context->setStrategy($bitcoin);
$context->executePayment(500.00);
