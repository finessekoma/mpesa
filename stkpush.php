<?php
session_start(); // Start session to store the CheckoutRequestID

include 'accessToken.php';
date_default_timezone_set('Africa/Nairobi');

$processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$callbackurl = 'https://e64a-41-90-187-186.ngrok-free.app/MPEsa-Daraja-Api/callback.php';
$passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
$BusinessShortCode = '174379';
$Timestamp = date('YmdHis');
$Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);

$phone = $_POST['phonenumber']; 
$money = $_POST['amount'];
$PartyA = $phone;
$PartyB = $BusinessShortCode;
$AccountReference = 'UMESKIA SOFTWARES';
$TransactionDesc = 'stkpush test';
$Amount = $money;

$stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
$curl_post_data = array(
  'BusinessShortCode' => $BusinessShortCode,
  'Password' => $Password,
  'Timestamp' => $Timestamp,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => $Amount,
  'PartyA' => $PartyA,
  'PartyB' => $BusinessShortCode,
  'PhoneNumber' => $PartyA,
  'CallBackURL' => $callbackurl,
  'AccountReference' => $AccountReference,
  'TransactionDesc' => $TransactionDesc
);

$data_string = json_encode($curl_post_data);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $processrequestUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, $stkpushheader);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

$curl_response = curl_exec($curl);
$data = json_decode($curl_response);

if (isset($data->CheckoutRequestID)) {
    $_SESSION['CheckoutRequestID'] = $data->CheckoutRequestID; 
        echo "The CheckoutRequestID for this transaction is: " . $data->CheckoutRequestID;
} else {
    echo "Transaction failed. Response Code: " . $data->ResponseCode . ". Error Message: " . $data->errorMessage;
}

curl_close($curl);
?>
