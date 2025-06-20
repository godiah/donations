<?php

namespace App\Channels;

use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    protected $africasTalking;

    public function __construct()
    {
        $this->africasTalking = new AfricasTalking(
            config('services.africastalking.username'),
            config('services.africastalking.key')
        );
    }

    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWhatsapp')) {
            Log::warning('toWhatsapp method not defined for notification.', [
                'notification' => get_class($notification),
            ]);
            return;
        }

        $individual = $notification->getIndividual();
        $phone = $individual->phone;

        if (!$phone) {
            Log::warning('No phone number provided for WhatsApp notification.', [
                'notification' => get_class($notification),
            ]);
            return;
        }

        // Validate phone number format (e.g., E.164: +254...)
        if (!preg_match('/^\+\d{10,15}$/', $phone)) {
            Log::warning('Invalid phone number format for WhatsApp.', [
                'phone' => $phone,
                'notification' => get_class($notification),
            ]);
            return;
        }

        $message = $notification->toWhatsapp($notifiable);

        try {
            $sms = $this->africasTalking->sms();
            $sms->send([
                'to' => "whatsapp:{$phone}",
                'message' => $message,
                'from' => config('services.africastalking.whatsapp_number'),
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp sending failed: ' . $e->getMessage(), [
                'phone' => $phone,
                'message' => $message,
                'notification' => get_class($notification),
            ]);
        }
    }
}
