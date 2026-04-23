<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature   = 'mail:test {to}';
    protected $description = 'Send a test email to verify Gmail SMTP config';

    public function handle(): void
    {
        $to = $this->argument('to');

        $this->info("Sending test email to {$to} ...");

        try {
            Mail::send([], [], function ($message) use ($to) {
                $message->to($to)
                    ->subject('MCC Voting System — Gmail SMTP Test')
                    ->html('
                        <div style="font-family:Arial,sans-serif;padding:24px;max-width:480px;margin:auto;background:#f9fafb;border-radius:10px;">
                            <h2 style="color:#152B66;">MCC Voting System</h2>
                            <p>Gmail SMTP is working correctly.</p>
                            <p style="color:#6b7280;font-size:13px;">Sent from: thesismcc@gmail.com</p>
                        </div>
                    ');
            });

            $this->info('✓ Email sent successfully!');
        } catch (\Exception $e) {
            $this->error('✗ Failed: ' . $e->getMessage());
        }
    }
}
