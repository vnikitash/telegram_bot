<?php

const TOKEN = "";
const KALVIN = 275.15;
const METHOD_SEND_MESSAGE = 'sendMessage';


$apiHost = "https://api.telegram.org/bot%s/%s";

$jsonData = file_get_contents("php://input");

file_put_contents("log.txt", $jsonData . PHP_EOL, FILE_APPEND);

$array = json_decode($jsonData, true);
$text = trim($array['message']['text']);

if (strpos($text, '/guess') !== false) {

    $int = rand(1,3);
    $parts = explode(" ", $text);
    $userNumber = (int) $parts[1];

    $text = ($int === $userNumber) ? 'Correct!' : 'WRONG!';

    $text .= ' Computer guessed '. $int . '. Your guess: ' . $userNumber;

    $url = sprintf($apiHost, TOKEN, METHOD_SEND_MESSAGE);


    $payload = json_encode([
        "chat_id"   => $array['message']['from']['id'],
        "text"      => $text,
    ]);

    $ch = curl_init($url);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);

    curl_exec($ch);
    curl_close($ch);
}

if (strpos($text, '/weather') !== false) {
    $url = sprintf($apiHost, TOKEN, METHOD_SEND_MESSAGE);

    $parts = explode(" ", $text);
    $city = $parts[1];

    $apiKey = "NEW KEY!!!";

    $json = file_get_contents("https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey");

    $response = json_decode($json, true);

    $country = $response['sys']['country'];
    $city = $response['name'];
    $long = $response['coord']['lon'];
    $lat = $response['coord']['lat'];
    $description = $response['weather'][0]['main'];
    $currentTemp = $response['main']['temp'] - KALVIN;
    $minT = $response['main']['temp_min'] - KALVIN;
    $maxT = $response['main']['temp_max'] - KALVIN;
    $humidity = $response['main']['humidity'] . '%';

    $text = ("$city [$country] ($long;$lat); T: $currentTemp (C) [$minT (C) - $maxT (C)] - $description. Humidity: $humidity");

    $payload = json_encode([
        "chat_id"   => $array['message']['from']['id'],
        "text"      => $text,
    ]);

    $ch = curl_init($url);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);

    curl_exec($ch);
    curl_close($ch);
}
