<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewHash extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public string $url)
    {
    }

    public function build(): NewHash
    {
        return $this->markdown('mail.new-hash', ['url' => $this->url])->subject(
            'Neuer Zugriffscode für DPAINS',
        );
    }
}
