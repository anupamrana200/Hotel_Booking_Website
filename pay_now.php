<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

date_default_timezone_set("Asia/Kolkata");

session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
}

if (isset($_POST['pay_now'])) {

    $TXN_AMOUNT = $_SESSION['room']['payment'];
    $NAME = $_SESSION['uName'];
    $BUYER_PHONE = $_SESSION['uPhone'];
    $res = selectALL('user_cred');
    $row = mysqli_fetch_assoc($res);
    $BUYER_EMAIL = $row['email'];
    


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
    $payload = array();

    $payload["purpose"] = ' ROOM BOOKING IN AS HOTEL';
    $payload["amount"] = $TXN_AMOUNT;
    $payload["phone"] = $BUYER_PHONE;
    $payload["buyer_name"] = $NAME;
    $payload["redirect_url"] = 'http://localhost/hbWebsite/insta_payment_status.php';
    $payload["send_email"] = true;
    $payload["webhook"] = 'http://www.example.com/webhook/';
    $payload["send_sms"] = true;
    $payload["email"] = $BUYER_EMAIL;
    $payload["allow_repeated_payments"] = false;

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
    $response = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($response, true);

    // echo "<pre>";
    // print_r($response);
    // echo "</pre>";

    // var_dump($response['success']);
    // var_dump($response['payment_request']['buyer_name']);
    // print_r($_SESSION); 
    // $frm_data = filteration($_GET);
    // var_dump($frm_data);
    // var_dump($_SESSION['room']['payment']);
    //      print_r($frm_data);
    // echo "<br>";
    // print_r($_SESSION);
    // echo "<br>";
    // var_dump($response['payment_request']['id']);


    //INSERT PAYMENT DATA TO DATABASE

    $frm_data = filteration($_POST);

   
    $query1 = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`, `order_id`) VALUES (?,?,?,?,?)";

    insert($query1,[$_SESSION['uId'],$_SESSION['room']['id'],$frm_data['checkin'],$frm_data['checkout'],$response['payment_request']['id']],'issss');

    $booking_id = mysqli_insert_id($con);

    $query2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?)";
    insert($query2,[$booking_id,$_SESSION['room']['name'],$_SESSION['room']['price'],$TXN_AMOUNT,$frm_data['name'],$frm_data['phonenum'],$frm_data['address']],'issssss');


    $_SESSION['prid'] = $response['payment_request']['id'];
    $_SESSION['tramount'] = $response['payment_request']['amount'];

    if ($response['success']) {
        $longurl = $response['payment_request']['longurl'];
        //echo $longurl;
        header("Location: $longurl");
        exit();
    }


}
?>