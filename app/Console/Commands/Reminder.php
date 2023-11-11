<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Reminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Define the FCM endpoint
        $fcmEndpoint = 'https://fcm.googleapis.com/fcm/send';

        // Define the FCM data payload
        $data = [
            'to' => '/topics/imron',
            'notification' => [
                'title' => 'Reminder',
                'body' => 'Hafalamu belum sesuai target harian, segera hafalkan!',
            ]
        ];

        // Send a POST request to the FCM endpoint
        $response = Http::withHeaders([
            'Authorization' => 'Bearer AAAA7GkmVb8:APA91bHpHa6AiLapZWJi9BmbIoyfWzOwjVmEp_YHtczhiWD2baOUeJxsyXgX8z9k9tmFvFw9yAoybuhcl9sUKdfR1Fbs1sxOKJLfujp1GAtOXOopxNhPoPijVpI7Zs1b0_gi5iiNvh6V',
            'Content-Type' => 'application/json',
        ])->post($fcmEndpoint, $data);

        // Check if the request was successful
        if ($response->successful()) {
            return [
                'status_code' => 200,
                'message' => 'Notification sent successfully',
                'response' => $response->json(),
            ];
        } else {
            return [
                'status_code' => 500,
                'message' => 'Failed to send notification',
                'response' => $response->json(),
            ];
        }
    }
}
