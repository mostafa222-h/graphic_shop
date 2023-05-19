<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOrderedImages extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private array $images,private User $user)
    {
        
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Send Ordered Images',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        foreach($this->images as $filePath)
        {
          $image  = storage_path('app/local_storage/' . $filePath);
       
       return $email =  new Content(
            view: 'mail.sendOrderedImages',
            with: [ 
                'user' => $this->user ,
                'image' => $image
            ]
        );

    }
        // foreach($this->images as $filePath)
        // {
        //   storage_path('app/local_storage/' . $filePath);
        // }
        // return $email ;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
