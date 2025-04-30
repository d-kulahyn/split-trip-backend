<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\FirebaseException;
use Illuminate\Support\Facades\Log;

class SendTestPushNotification extends Command
{
    protected $signature = 'fcm:test {token}';
    protected $description = 'Send test push notification via FCM';

    public function handle(): void
    {
        $token = $this->argument('token');
        $this->info("Starting FCM test push to token: {$token}");

        $path = base_path('firebase.json');
        $this->info("Using service account at: {$path}");

        if (!file_exists($path)) {
            $this->error("firebase.json not found at: {$path}");
            return;
        }

        try {
            $factory = (new Factory())->withServiceAccount($path);
            $messaging = $factory->createMessaging();

            $message = CloudMessage::new()
                ->withNotification([
                    'title' => '🔥 Test Push',
                    'body' => '✅ If you see this — FCM is working!',
                ])
                ->withData([
                    'custom_key' => 'test_value',
                ])
                ->withChangedTarget('token', $token);

            $this->info("Sending push...");
            $messaging->send($message);

            $this->info("✅ Push sent successfully");
        } catch (MessagingException $e) {
            $this->error("MessagingException: " . $e->getMessage());
            Log::error('MessagingException', ['exception' => $e]);
        } catch (FirebaseException $e) {
            $this->error("FirebaseException: " . $e->getMessage());
            Log::error('FirebaseException', ['exception' => $e]);
        } catch (\Throwable $e) {
            $this->error("Unexpected exception: " . $e->getMessage());
            Log::error('UnexpectedException', ['exception' => $e]);
        }
    }
}
