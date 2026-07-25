<?php
// Function para mag-send ng totoong SMS alert sa magulang
function sendSMSNotification($contact_number, $message) {
    $apiKey = "YOUR_SEMAPHORE_API_KEY"; // Ilagay ang API Key dito

    $ch = curl_init();
    $parameters = array(
        'apikey' => $apiKey,
        'number' => $contact_number,
        'message' => $message,
        'sendername' => 'SEMAPHORE'
    );

    curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}
?>