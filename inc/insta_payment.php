<?php
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://test.instamojo.com/api/1.1/payment-requests/');
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    array(
        "X-Api-Key:test_f8d0feb51dfd732312562b8ef01",
        "X-Auth-Token:test_97a0c21fa99b35a9301f9efadc0"
    )
);
$payload = array(
    'purpose' => 'FIFA 16',
    'amount' => '2500',
    'phone' => '7063631178',
    'buyer_name' => 'Anupam Rana',
    'redirect_url' => 'http://localhost/insta/payment_status.php',
    'send_email' => true,
    'webhook' => 'http://www.example.com/webhook/',
    'send_sms' => true,
    'email' => 'anupamrana200@gmail.com',
    'allow_repeated_payments' => false
);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
$response = curl_exec($ch);
curl_close($ch);

$response=json_decode($response, true);

// echo "<pre>";
// print_r($response);
// echo "</pre>";

// var_dump($response['success']);

if($response['success']){
    $longurl = $response['payment_request']['longurl'];
    //echo $longurl;
    header("Location: $longurl");
    exit();
}


// echo '<pre>';hbh
// print_r($response->payment_request->longurl);
// $_SESSION['TID']=$response->payment_request->id;
// header('location:'.$response->payment_request->longurl);

?>