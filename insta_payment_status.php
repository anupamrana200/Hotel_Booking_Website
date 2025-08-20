<?php

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

date_default_timezone_set("Asia/Kolkata");

session_start();
unset($_SESSION['room']);

function regenerate_session($uid)
{
    $user_q = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$uid], 'i');
    $user_fetch = mysqli_fetch_assoc($user_q);

    $_SESSION['login'] = true;
    $_SESSION['uId'] = $user_fetch['id'];
    $_SESSION['uName'] = $user_fetch['name'];
    $_SESSION['uPic'] = $user_fetch['profile'];
    $_SESSION['uPhone'] = $user_fetch['phonenum'];

}

$slct_query = "SELECT `booking_id`, `user_id` FROM `booking_order` WHERE `order_id`='$_SESSION[prid]'";

$slct_res = mysqli_query($con, $slct_query);

if (mysqli_num_rows($slct_res) == 0) {
    redirect('index.php');
}

$slct_fetch = mysqli_fetch_assoc($slct_res);

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    //regenerate session
    regenerate_session($slct_fetch['user_id']);
}

if ($_GET['payment_status'] == "Credit") {
    $upd_query = "UPDATE `booking_order` SET `booking_status`='booked',`trans_id`='$_GET[payment_id]',`trans_amt`='$_SESSION[tramount]',
    `trans_status`='$_GET[payment_status]',`trans_req_id`='$_GET[payment_request_id]' WHERE `booking_id` = '$slct_fetch[booking_id]'";

    mysqli_query($con, $upd_query);
    redirect('pay_status.php?order=' . $_SESSION['prid']);
} else {
    $upd_query = "UPDATE `booking_order` SET `booking_status`='payment failed',`trans_id`='$_GET[payment_id]',`trans_amt`='$_SESSION[tramount]',
    `trans_status`='$_GET[payment_status]',`trans_req_id`='$_GET[payment_request_id]' WHERE `booking_id` = '$slct_fetch[booking_id]'";

    mysqli_query($con, $upd_query);
    redirect('pay_status.php?order=' . $_SESSION['prid']);

}

// $payment_id = $_GET['payment_id'];
// $payment_status = $_GET['payment_status'];
// $payment_request_id = $_GET['payment_request_id'];

// echo "
//     payment id : $payment_id<br>
//     payment status : $payment_status<br>
//     payment_request id: $payment_request_id<br>
//     pr id : $_SESSION[prid]<br>
//     tr amount : $_SESSION[tramount]

// ";







?>