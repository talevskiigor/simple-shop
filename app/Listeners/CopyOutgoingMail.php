<?php
namespace App\Listeners;
use Illuminate\Mail\Events\MessageSending;
class CopyOutgoingMail {
    public function handle(MessageSending $event): void {
        $copy = trim((string) config('mail.copy_to'));
        if (!$copy || !filter_var($copy, FILTER_VALIDATE_EMAIL)) return;
        foreach ([...$event->message->getTo(), ...$event->message->getCc(), ...$event->message->getBcc()] as $address) {
            if (strcasecmp($address->getAddress(), $copy) === 0) return;
        }
        $event->message->addBcc($copy);
    }
}
