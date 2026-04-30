<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationHelper
{
    public static function kirimPemasukan($user, float $nominal)
    {
        if (!$user->fcm_token) return;

        $factory = (new Factory)->withServiceAccount(
            env('FIREBASE_CREDENTIALS')
        );
        $messaging = $factory->createMessaging();

        $message = CloudMessage::new()
            ->withToken($user->fcm_token)
            ->withNotification(
                Notification::create(
                    '💰 Pemasukan Baru Masuk!',
                    '+' . 'Rp ' . number_format($nominal, 0, ',', '.') .
                        ' telah diterima'
                )
            )
            ->withData([
                'type' => 'pemasukan',
                'nominal' => (string) $nominal,
            ]);

        try {
            $messaging->send($message);
        } catch (\Exception $e) {
            Log::error('FCM Error: ' . $e->getMessage());
        }
    }
}
