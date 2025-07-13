<?php

namespace App\Channels;

use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WhatsappChannel
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

        // Determine phone number source based on notification type
        $phone = $this->getPhoneNumber($notification);

        if (!$phone) {
            Log::warning('No phone number provided for WhatsApp notification.', [
                'notification' => get_class($notification),
            ]);
            return;
        }

        $message = $notification->toWhatsapp($notifiable);
        $whatsappNumber = config('services.africastalking.whatsapp_number');

        if (!$whatsappNumber) {
            Log::error('WhatsApp sender number not configured', [
                'notification' => get_class($notification),
            ]);
            return;
        }

        try {
            $sms = $this->africasTalking->sms();
            $result = $sms->send([
                'to' => $phone,
                'message' => $message,
                'from' => $whatsappNumber, // Use WhatsApp-enabled number
            ]);

            Log::info('WhatsApp message sent successfully', [
                'phone' => $phone,
                'notification' => get_class($notification),
                'whatsapp_number' => $whatsappNumber,
                'result' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp sending failed: ' . $e->getMessage(), [
                'phone' => $phone,
                'message' => $message,
                'notification' => get_class($notification),
                'whatsapp_number' => $whatsappNumber,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get phone number from different notification types
     */
    protected function getPhoneNumber(Notification $notification): ?string
    {
        // Handle your existing application notifications
        if (method_exists($notification, 'getIndividual')) {
            $individual = $notification->getIndividual();
            return $individual->phone ?? null;
        }

        // Handle new donation notifications
        if (method_exists($notification, 'getContribution')) {
            $contribution = $notification->getContribution();
            return $this->formatPhoneNumber($contribution->phone ?? null);
        }

        // Handle other notification types that might have direct phone access
        if (isset($notification->phone)) {
            return $this->formatPhoneNumber($notification->phone);
        }

        return null;
    }

    /**
     * Format phone number for Africa's Talking WhatsApp API
     */
    protected function formatPhoneNumber(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-digit characters
        $cleanPhone = preg_replace('/\D/', '', $phone);

        // Handle Kenyan numbers
        if (strlen($cleanPhone) === 10 && str_starts_with($cleanPhone, '0')) {
            // Convert 0712345678 to +254712345678
            return '+254' . substr($cleanPhone, 1);
        } elseif (strlen($cleanPhone) === 9) {
            // Convert 712345678 to +254712345678
            return '+254' . $cleanPhone;
        } elseif (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '254')) {
            // Convert 254712345678 to +254712345678
            return '+' . $cleanPhone;
        } elseif (str_starts_with($phone, '+')) {
            // Already properly formatted
            return $phone;
        }

        // For international numbers, validate basic format
        if (strlen($cleanPhone) >= 10 && strlen($cleanPhone) <= 15) {
            return '+' . $cleanPhone;
        }

        return null; // Invalid format
    }
}
