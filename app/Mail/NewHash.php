<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewHash extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function build(): NewHash
    {
        return $this->markdown('mail.new-hash', ['url' => $this->url])
            ->subject('Neuer Zugriffscode für DPAINS');
    }
}
