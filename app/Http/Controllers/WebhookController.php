<?php

namespace App\Http\Controllers;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Stripe\Event;

class WebhookController extends CashierController
{
    /**
     * Handle customer subscription updated
     */
    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        // Custom logic when subscription changes
        // Example: Send email notification to admin
        
        return parent::handleCustomerSubscriptionUpdated($payload);
    }
    
    /**
     * Handle invoice payment succeeded
     */
    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        // Custom logic for successful payments
        // Example: Update local database with payment record
        
        return parent::handleInvoicePaymentSucceeded($payload);
    }
}