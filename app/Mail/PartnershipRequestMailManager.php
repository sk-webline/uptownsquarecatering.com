<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PartnershipRequestMailManager extends Mailable
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
    return $this->view('emails.partnership_request')
      ->from($this->array['from'], env('MAIL_FROM_NAME'))
      ->subject($this->array['subject'])
      ->with([
        'content' => $this->array['content'],
        'name' => $this->array['name'],
        'company' => $this->array['company'],
        'country' => $this->array['country'],
        'city' => $this->array['city'],
        'phone' => $this->array['phone'],
        'interests' => $this->array['interests'],
        'sender' => $this->array['sender'],
        'details' => $this->array['details']
      ]);
  }
}