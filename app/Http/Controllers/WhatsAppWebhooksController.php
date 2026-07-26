<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final class WhatsAppWebhooksController extends Controller
{
    /**
     * 1. Meta Webhook Verification (GET Request)
     * This method runs once when you click 'Verify' in Meta Dashboard.
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $localToken = env('WHATSAPP_WEBHOOK_VERIFY_TOKEN');

        if ($mode === 'subscribe' && $token === $localToken) {
            Log::info('WhatsApp Webhook successfully verified.');

            // Meta explicitly expects a raw string response of the challenge variable with a 200 code
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('WhatsApp Webhook verification failed. Invalid token.');

        return response('Unauthorized', 403);
    }

    /**
     * 2. Handle Incoming Events (POST Request)
     * Captures status updates (sent, delivered, read) and incoming user text messages.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        defer(function () use ($payload) {
            try {
                if (! isset($payload['entry'][0]['changes'][0]['value'])) {
                    return;
                }

                $value = $payload['entry'][0]['changes'][0]['value'];

                // Scenario A: Process Message Status Updates (sent, delivered, read, failed)
                if (isset($value['statuses'])) {
                    foreach ($value['statuses'] as $status) {
                        Log::info('WhatsApp Message Status Processed:', [
                            'wamid' => $status['id'],
                            'status' => $status['status'],
                        ]);
                        $payload = Cache::get("wa_msg_{$status['id']}");

                        // Execute internal database update logic here
                    }
                }

                // Scenario B: Process Incoming Text Messages from Users
                if (isset($value['messages'])) {
                    foreach ($value['messages'] as $message) {
                        if (($message['type'] ?? '') === 'text') {
                            Log::info('WhatsApp incoming Message Processed:', [
                                'from' => $message['from'],
                                'body' => $message['text']['body'],
                            ]);
                            // Execute chatbot or notification logic here
                        }
                    }
                }
            } catch (Exception $e) {
                Log::error('Error processing WhatsApp Webhook: '.$e->getMessage());
            }
        });

        return response('EVENT_RECEIVED', 200);
    }
}
