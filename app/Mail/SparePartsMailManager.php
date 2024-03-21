<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SparePartsMailManager extends Mailable
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
    return $this->view('emails.spare_parts')
      ->from($this->array['from'], env('MAIL_FROM_NAME'))
      ->subject($this->array['subject'])
      ->with([
        'content' => $this->array['content'],
        'brand' => $this->array['brand'],
        'model_code' => $this->array['model_code'],
        'model_year' => $this->array['model_year'],
        'chassis_no' => $this->array['chassis_no'],
        'color_code' => $this->array['color_code'],
        'name' => $this->array['name'],
        'country' => $this->array['country'],
        'sender' => $this->array['sender'],
        'details' => $this->array['details']
      ]);
  }
}