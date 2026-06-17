<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseService
{
    /**
     * Kirim notifikasi menggunakan Firebase Cloud Messaging (HTTP v1 API via Package)
     */
    public static function sendNotification($token, $title, $body, $data = [])
    {
        if (!$token) {
            return false;
        }

        try {
            $messaging = Firebase::messaging();

            $notification = Notification::create($title, $body);

            // Semua value dalam $data harus bertipe string untuk FCM
            $stringData = array_map('strval', $data);

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($stringData);

            $messaging->send($message);

            return true;
        } catch (\Exception $e) {
            Log::error('FCM Send Error: ' . $e->getMessage());
            return false;
        }
    }
}
