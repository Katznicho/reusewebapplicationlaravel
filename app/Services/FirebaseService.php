<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class FirebaseService
{
    private $projectID;

    private $baseURL;

    private $endpoint;

    private $url;

    private $scopes;

    public function __construct()
    {
        $this->projectID = 'reuse-f0081';
        $this->baseURL = 'https://fcm.googleapis.com';
        $this->endpoint = 'v1/projects/'.$this->projectID.'/messages:send';
        $this->url = $this->baseURL.'/'.$this->endpoint;
        $this->scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
    }

    public function getAccessToken()
    {
        $serviceAccountPath = storage_path('reuse_service.json');
        $credentials = new ServiceAccountCredentials($this->scopes, $serviceAccountPath);

        return $credentials->fetchAuthToken();
    }

    private function sendFCMMessage($fcmMessage)
    {
        $headers = [
            'Authorization' => 'Bearer '.$this->getAccessToken()['access_token'],
            'Content-Type' => 'application/json; UTF-8',
        ];

        return $this->getAccessToken()['access_token'];

        $httpClient = new Client();
        $response = $httpClient->request('POST', $this->url, [
            'headers' => $headers,
            'json' => $fcmMessage,
        ]);

        if ($response->getStatusCode() === 200) {
            echo "Message sent to Firebase for delivery, response:\n";

            return $response->getBody()."\n";
        } else {
            echo "Unable to send message to Firebase\n";

            return $response->getBody()."\n";
        }
    }

    private function buildCommonMessage()
    {
        return [
            'message' => [
                'topic' => 'news',
                'notification' => [
                    'title' => 'FCM Notification',
                    'body' => 'Notification from FCM',
                ],
            ],
        ];
    }

    private function buildOverrideMessage()
    {
        $fcmMessage = $this->buildCommonMessage();

        $apnsOverride = [
            'payload' => [
                'aps' => [
                    'badge' => 1,
                ],
            ],
            'headers' => [
                'apns-priority' => '10',
            ],
        ];

        $androidOverride = [
            'notification' => [
                'click_action' => 'android.intent.action.MAIN',
            ],
        ];

        $fcmMessage['message']['android'] = $androidOverride;
        $fcmMessage['message']['apns'] = $apnsOverride;

        return $fcmMessage;
    }

    public function sendCommonMessage()
    {
        $commonMessage = $this->buildCommonMessage();
        echo "FCM request body for message using common notification object:\n";
        echo json_encode($commonMessage, JSON_PRETTY_PRINT)."\n";
        $this->sendFCMMessage($commonMessage);
    }

    public function sendOverrideMessage()
    {
        $overrideMessage = $this->buildOverrideMessage();
        echo "FCM request body for override message:\n";
        echo json_encode($overrideMessage, JSON_PRETTY_PRINT)."\n";
        $this->sendFCMMessage($overrideMessage);
    }

    /**
     * Sends a push notification to a specific device.
     *
     * @param string $deviceToken The token of the device to send the notification to.
     * @param string $title The title of the notification.
     * @param string $body The body of the notification.
     * @throws Some_Exception_Class Description of the exception that can be thrown.
     * @return Some_Return_Value The value returned by the sendFCMMessage function.
     */
    public function sendToDevice($deviceToken, $title, $body)
    {
        $fcmMessage = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ],
        ];

        return $this->sendFCMMessage($fcmMessage);
    }
}
