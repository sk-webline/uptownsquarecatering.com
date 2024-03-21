<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UsedProductRequestMailManager extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   *
   * @return void
   */

  public $array;

  public function __construct($array)
  {
    $this->array = $array;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->view('emails.used_product_request')
      ->from($this->array['from'], env('MAIL_FROM_NAME'))
      ->subject($this->array['subject'])
      ->with([
        'content' => $this->array['content'],
        'name' => $this->array['name'],
        'product' => $this->array['product'],
        'country' => $this->array['country'],
        'sender' => $this->array['sender'],
        'details' => $this->array['details']
      ]);
  }
}