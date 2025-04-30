<?php

namespace App\Http\Controllers\Backend\Dispatcher\Evropolis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\Facades\MQTT;
use PhpMqtt\Client\MqttClient;

class DispatcherEvropolisController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Axe::class, 'axe');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // Create an instance of a PSR-3 compliant logger. For this example, we will also use the logger to log exceptions.
        //$logger = new SimpleLogger(LogLevel::INFO);

        try {
            // Create a new instance of an MQTT client and configure it to use the shared broker host and port.
            $client = new MqttClient('192.168.3.10', '1883', 'test-subscriber', MqttClient::MQTT_3_1, null, null);

            // Connect to the broker without specific connection settings but with a clean session.
            $client->connect(null, true);

            // Subscribe to the topic 'foo/bar/baz' using QoS 0.
            $client->subscribe('/devices/hwmon/controls/Board Temperature', function (string $topic, string $message, bool $retained) use ($client) {

                Log::info('Data from MQTT Wiren Board', [
                    'topic' => $topic,
                    'message' => $message,
                ]);

                // After receiving the first message on the subscribed topic, we want the client to stop listening for messages.
                $client->interrupt();
            }, MqttClient::QOS_AT_MOST_ONCE);

            // Since subscribing requires to wait for messages, we need to start the client loop which takes care of receiving,
            // parsing and delivering messages to the registered callbacks. The loop will run indefinitely, until a message
            // is received, which will interrupt the loop.
            $client->loop(true);

            // Gracefully terminate the connection to the broker.
            $client->disconnect();
        } catch (MqttClientException $e) {
            // MqttClientException is the base exception of all exceptions in the library. Catching it will catch all MQTT related exceptions.
            //$logger->error('Subscribing to a topic using QoS 0 failed. An exception occurred.', ['exception' => $e]);
            Log::error($e);
        }




    }
}
