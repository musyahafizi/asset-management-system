<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $staffName;
    public $itemName;
    public $deadline;

    public function __construct($staffName, $itemName, $deadline)
    {
        $this->staffName = $staffName;
        $this->itemName = $itemName;
        $this->deadline = $deadline;
    }

    public function build()
    {
        return $this->subject('Reminder: Item Return Deadline Near')
                    ->html("<h2>Hello, {$this->staffName}</h2>
                            <p>This is a reminder to return the <b>{$this->itemName}</b> by <b>{$this->deadline}</b>.</p>
                            <p>Please return it via the staff portal to update inventory.</p>");
    }
}