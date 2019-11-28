<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckoutMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $products;
    public $price;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $products, $price)
    {
        $this->data = $data;
        $this->products = $products;
        $this->price = $price;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.checkout.checkout-form');
    }
}
