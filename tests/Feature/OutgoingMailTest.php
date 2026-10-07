<?php
namespace Tests\Feature;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
use Tests\TestCase;
class OutgoingMailTest extends TestCase {
    public function test_all_outgoing_messages_keep_recipients_and_add_one_private_copy(): void {
        config(['mail.copy_to' => 'igor.talevski+forkids@gmail.com']);
        $sent = Mail::raw('A test message', fn ($m) => $m->to('customer@example.test')->subject('Test')->bcc('existing@example.test'));
        $message = $sent->getSymfonySentMessage()->getOriginalMessage();
        $this->assertSame('customer@example.test', $message->getTo()[0]->getAddress());
        $this->assertSame(['existing@example.test','igor.talevski+forkids@gmail.com'], array_map(fn($a) => $a->getAddress(), $message->getBcc()));
        $event = new MessageSending($message); app(\App\Listeners\CopyOutgoingMail::class)->handle($event);
        $this->assertCount(2, $message->getBcc());
    }
    public function test_copy_address_already_in_to_is_not_added_again(): void {
        config(['mail.copy_to' => 'igor.talevski+forkids@gmail.com']);
        $m = (new Email)->to('igor.talevski+forkids@gmail.com');
        app(\App\Listeners\CopyOutgoingMail::class)->handle(new MessageSending($m));
        $this->assertCount(0, $m->getBcc());
    }
}
