<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TextBeeSmsService
{
    public function sendReadyForPickupNotification(string $phoneNumber, string $customerName, int $orderId): void
    {
        $apiKey = config('services.textbee.api_key');

        if (! is_string($apiKey) || trim($apiKey) === '') {
            Log::warning('TextBee is not configured; order-ready SMS was skipped.', [
                'order_id' => $orderId,
            ]);

            return;
        }

        $recipient = $this->normalizePhilippinePhoneNumber($phoneNumber);

        if ($recipient === null) {
            Log::warning('Order-ready SMS was skipped because the customer phone number is invalid.', [
                'order_id' => $orderId,
            ]);

            return;
        }

        $messageEnding = ', your I Laba U laundry order is ready for pickup. Please pay the remaining balance before claiming it.';
        $customerNameLength = max(0, 120 - mb_strlen('Hi '.$messageEnding));
        $customerName = mb_substr(trim($customerName), 0, $customerNameLength);
        $message = "Hi {$customerName}{$messageEnding}";
        $payload = [
            'recipients' => [$recipient],
            'message' => $message,
        ];
        $deviceId = config('services.textbee.device_id');

        if (is_string($deviceId) && trim($deviceId) !== '') {
            $payload['deviceId'] = $deviceId;
        }

        try {
            $response = Http::connectTimeout(3)
                ->timeout(8)
                ->withHeaders(['x-api-key' => $apiKey])
                ->acceptJson()
                ->post(
                    rtrim((string) config('services.textbee.base_url'), '/').'/gateway/send-sms',
                    $payload,
                );
        } catch (ConnectionException $exception) {
            report($exception);

            Log::warning('TextBee could not be reached; order-ready SMS was not queued.', [
                'order_id' => $orderId,
            ]);

            return;
        }

        if (! $response->successful()) {
            Log::warning('TextBee rejected the order-ready SMS request.', [
                'order_id' => $orderId,
                'status' => $response->status(),
            ]);
        }
    }

    private function normalizePhilippinePhoneNumber(string $phoneNumber): ?string
    {
        $digits = preg_replace('/\D+/', '', $phoneNumber);

        if (! is_string($digits)) {
            return null;
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '09')) {
            $digits = '63'.substr($digits, 1);
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            $digits = '63'.$digits;
        }

        if (strlen($digits) !== 12 || ! str_starts_with($digits, '639')) {
            return null;
        }

        return '+'.$digits;
    }
}
