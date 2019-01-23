<?php

/**
 * This is a simple PHP class for the MPesa API.
 * 
 * @package Mpesa API Client
 * @license http://mit.com/licence MIT LICENCE
 * @author Ben Muriithi <benmuriithi929@gmail.com>
 */

namespace App\Http\Controllers;

use Log;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MpesaController extends Controller
{
    /**
     * The common part of the MPesa API endpoints
     * @var string $base_url
     */
    private $base_url;
    /**
     * The consumer key
     * @var string $consumer_key
     */
    private $consumer_key;
    /**
     * The consumer key secret
     * @var string $consumer_secret
     */
    private $consumer_secret;
    /**
     * The MPesa Paybill number
     * @var int $paybill
     */
    private $paybill;
    /**
     * The Lipa Na MPesa paybill number
     * @var int $lipa_na_mpesa
     */
    private $lipa_na_mpesa;
    /**
     * The Lipa Na MPesa paybill number SAG Key
     * @var string $lipa_na_mpesa_key
     */
    private $lipa_na_mpesa_key;
    /**
     * The Mpesa portal Username
     * @var string $initiator_username
     */
    private $initiator_username;
    /**
     * The Mpesa portal Password
     * @var string $initiator_password
     */
    private $initiator_password;
    /**
     * The Callback common part of the URL eg "https://domain.com/callbacks/"
     * @var string $initiator_password
     */
    private $callback_baseurl;
    /**
     * The test phone number provided by safaricom. For developers
     * @var string $test_msisdn
     */
    private $test_msisdn;
    /**
     * The signed API credentials
     * @var string $cred
     */
    private $cred;
    
    /**
     * Construct method
     * 
     * Initializes the class with an array of API values.
     * 
     * @param array $config
     * @return void
     * @throws exception if the values array is not valid
     */
    
    public function __construct(){
        
        $this->base_url = 'https://sandbox.safaricom.co.ke/mpesa/'; //Base URL for the API endpoints. This is basically the 'common' part of the API endpoints
        $this->consumer_key = 'uKxU78Y9q2cFruO2fKRWuofRCObzMQh8';   //App Key. Get it at https://developer.safaricom.co.ke
        $this->consumer_secret = 'By9NUqT7NGhzy5Pj';                    //App Secret Key. Get it at https://developer.safaricom.co.ke
        $this->paybill = '603065';                                  //The paybill/till/lipa na mpesa number
        $this->lipa_na_mpesa = '174379';                                //Lipa Na Mpesa online checkout
        $this->lipa_na_mpesa_key = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';  //Lipa Na Mpesa online checkout password
        $this->initiator_username = 'test7';                    //Initiator Username. I dont where how to get this.
        $this->initiator_password = '4567';                 //Initiator password. I dont know where to get this either.
        
        $this->callback_baseurl = 'https://38653d24.ngrok.io/';
        $this->test_msisdn = '254708374149';
        
        $pubkey = file_get_contents(public_path() . '\cert\cert.cer');
        //$enc = '';
        openssl_public_encrypt($this->initiator_password, $output, $pubkey, OPENSSL_PKCS1_PADDING);
        //$enc .= $output;
        $this->cred = base64_encode($output);
        
        //We override the above $this->cred with the testing credentials
        //$this->cred = 'jQGehsgnujMdEnVOhGq3YdX72blQnpZ+RPgYhe15kU2+UiUkauYDbsxbv+rgVgK4nKU/90R6V7CZDx4+e6KcYQMKCwJht9FfdxG3gC8g2fgxlrCvR+RnObwLOBfJ9htDVyUCJjxP31J/RoC7j25N3g7WDRfcoDXrhRUmG9NGLua+leF6ssJrNxFv6S0aT8S1ihl3aueGAuZxWr7OnbagZZElPueAZKEs8IJDKCh4xkZVUevvUysZCZuHqchMKLYDv80zK/XJ46/Ja/7F1+Qw7180bR/XcptV3ttXV56kGvJ/GMp6FUUem32o2bJMvu+6AkqJnczj0QNq5ZVtTudjvg==';
    }

    /**
     * Submit Request
     * 
     * Handles submission of all API endpoints queries
     * 
     * @param string $url The API endpoint URL
     * @param json $data The data to POST to the endpoint $url
     * @return object|boolean Curl response or FALSE on failure
     * @throws exception if the Access Token is not valid
     */

    private function submit_request($url, $data){ // Returns cURL response
        
        $credentials = base64_encode($this->consumer_key.':'.$this->consumer_secret);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials, 'Content-Type: application/json'));
        $response = curl_exec($ch);
        print_r(curl_error($ch));
        curl_close($ch);
        print_r($response);
        
        $response = json_decode($response);
        
        $access_token = $response->access_token;
        
        // The above $access_token expires after an hour, find a way to cache it to minimize requests to the server
        if(!$access_token){
            throw new Exception("Invalid access token generated");
        }
        
        if($access_token != '' || $access_token !== FALSE){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_token));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }else{
            return FALSE;
        }
    }

    /**
     * Business to Client
     * 
     * This method is used to send money to the clients Mpesa account.
     * 
     * @param int $amount The amount to send to the client
     * @param int $phone The phone number of the client in the format 2547xxxxxxxx
     * @return object Curl Response from submit_request, FALSE on failure
     */

    public function b2c($amount, $phone){
        $request_data = array(
            'InitiatorName' => $this->initiator_username,
            'SecurityCredential' => $this->cred,
            'CommandID' => 'PromotionPayment',
            'Amount' => $amount,
            'PartyA' => $this->paybill,
            'PartyB' => $phone,
            'Remarks' => 'This is a test comment or remark',
            'QueueTimeOutURL' => 'https://dev.matrixcyber.co.ke/cb.php',
            'ResultURL' => 'https://dev.matrixcyber.co.ke/cb.php',
            'Occasion' => '' //Optional
        );
        $data = json_encode($request_data);
        $url = $this->base_url.'b2c/v1/paymentrequest';
        $response = $this->submit_request($url, $data);
        return $response;
    }

    /**
     * Business to Business
     * 
     * This method is used to send money to other business Mpesa paybills.
     * 
     * @param int $amount The amount to send to the business
     * @param int $shortcode The shortcode of the business to send to
     * @return object Curl Response from submit_request, FALSE on failure
     */

    public function b2b($amount, $shortcode){
        $request_data = array(
            'Initiator' => $this->initiator_username,
            'SecurityCredential' => $this->cred,
            'CommandID' => 'BusinessToBusinessTransfer',
            'SenderIdentifierType' => 'Shortcode',
            'RecieverIdentifierType' => 'Shortcode',
            'Amount' => 100,
            'PartyA' => $this->paybill,
            'PartyB' => 600000,
            'AccountReference' => 'Bennito',
            'Remarks' => 'This is a test comment or remark',
            'QueueTimeOutURL' => $this->callback_baseurl.'b2b/timeout.php',
            'ResultURL' => $this->callback_baseurl.'b2b/result_url.php',
        );
        $data = json_encode($request_data);
        $url = $this->base_url.'b2b/v1/paymentrequest';
        $response = $this->submit_request($url, $data);
        return $response;
    }

    /**
     * Client to Business
     * 
     * This method is used to register URLs for callbacks when money is sent from the MPesa toolkit menu
     * 
     * @param string $confirmURL The local URL that MPesa calls to confirm a payment
     * @param string $ValidationURL The local URL that MPesa calls to validate a payment
     * @return object Curl Response from submit_request, FALSE on failure
     */

    public function register_c2b(){
        $request_data = array(
            'ShortCode' => $this->paybill,
            'ResponseType' => 'Completed',
            'ConfirmationURL' => $this->callback_baseurl.'c2b/confirmation',
            'ValidationURL' => $this->callback_baseurl.'c2b/validation'
        );
        $data = json_encode($request_data);
        $url = $this->base_url.'c2b/v1/registerurl';
        $response = $this->submit_request($url, $data);
        return $response;
    }

    /**
     * C2B Simulation
     * 
     * This method is used to simulate a C2B Transaction to test your ConfirmURL and ValidationURL in the Client to Business method
     * 
     * @param int $amount The amount to send to Paybill number
     * @param int $msisdn A dummy Safaricom phone number to simulate transaction in the format 2547xxxxxxxx
     * @param string $ref A reference name for the transaction
     * @return object Curl Response from submit_request, FALSE on failure
     */

    public function simulate_c2b(Request $request){
        $data = array(
            'ShortCode' => $this->paybill,
            'CommandID' => 'CustomerPayBillOnline',
            'Amount' => $request->amount,
            'Msisdn' => $request->msisdn,
            'BillRefNumber' => $request->ref
        );
        $data = json_encode($data);
        $url = $this->base_url.'c2b/v1/simulate';
        $response = $this->submit_request($url, $data);
        return $response;
    }

    /**
     * C2B Validation
     * 
     * This method is used to validate a C2B Transaction aganist an various methods set by the developer
     * 
     * @param array $request from mpesa api
     * @return json respone for payment accepted or rejected
     */
    public function validate_c2b(Request $request) {
    	Log::info("validating");
        Log::info(print_r($request->all(),true));

        $data = (object) $request;
        $transaction_id = Carbon::now()->format('ymdis');

        $MoneyIn = TRUE;

        if ($MoneyIn) {
            return response()->json([
                "ResultCode" => 0,
                "ResultDesc" => "Payment Accepted",
                "ThirdPartyTransID" => $transaction_id
            ]);
        } else {
            return response()->json([
                "ResultCode" => 1,
                "ResultDesc" => "Payment Rejected",
                "ThirdPartyTransID" => $transaction_id
            ]);
        }
    }

    /**
     * C2B Confirmation
     * 
     * This method is used to confirm a C2B Transaction that has passed various methods set by the developer during validation
     * 
     * @param array $request from mpesa api
     * @return json respone for payment detials i.e transcation code and timestamps e.t.c
     */
    public function confirm_c2b(Request $request) {
    	Log::info("confirming");
        Log::info(print_r($request->all(), true));        
        return ;
    }

    /**
     * Check Balance
     * 
     * Check Paybill balance
     * 
     * @return object Curl Response from submit_request, FALSE on failure
     */
    public function check_balance(){
        $data = array(
            'CommandID' => 'AccountBalance',
            'PartyA' => $this->paybill,
            'IdentifierType' => '4',
            'Remarks' => 'Remarks or short description',
            'Initiator' => $this->initiator_username,
            'SecurityCredential' => $this->cred,
            'QueueTimeOutURL' => $this->callback_baseurl.'check_balance/callback',
            'ResultURL' => $this->callback_baseurl.'check_balance/callback'
        );
        $data = json_encode($data);
        $url = $this->base_url.'accountbalance/v1/query';
        $response = $this->submit_request($url, $data);
        return $response;
    }

    public function check_balance_callback(Request $request) {
    	Log::info("checking balance");
        Log::info(print_r($request->all(), true));        
        return ;
    }

    /**
     * Transaction status request
     * 
     * This method is used to check a transaction status
     * 
     * @param string $transaction ID eg LH7819VXPE
     * @return object Curl Response from submit_request, FALSE on failure
     */
     
    public function status_request($transaction = 'LH7819VXPE'){
        $data = array(
            'CommandID' => 'TransactionStatusQuery',
            'PartyA' => $this->paybill,
            'IdentifierType' => 4,
            'Remarks' => 'Testing API',
            'Initiator' => $this->initiator_username,
            'SecurityCredential' => $this->cred,
            'QueueTimeOutURL' => 'https://dev.matrixcyber.co.ke/cb.php',
            'ResultURL' => 'https://dev.matrixcyber.co.ke/cb.php',
            'TransactionID' => $transaction,
            'Occassion' => 'Test'
        );
        $data = json_encode($data);
        $url = $this->base_url.'transactionstatus/v1/query';
        $response = $this->submit_request($url, $data);
        return $response;
    }

    /**
     * Transaction Reversal
     * 
     * This method is used to reverse a transaction
     * 
     * @param int $receiver Phone number in the format 2547xxxxxxxx
     * @param string $trx_id Transaction ID of the Transaction you want to reverse eg LH7819VXPE
     * @param int $amount The amount from the transaction to reverse
     * @return object Curl Response from submit_request, FALSE on failure
     */
     
    public function reverse_transaction($receiver, $trx_id, $amount){
        $data = array(
            'CommandID' => 'TransactionReversal',
            'ReceiverParty' => $this->test_msisdn,
            'RecieverIdentifierType' => 1, //1=MSISDN, 2=Till_Number, 4=Shortcode
            'Remarks' => 'Testing',
            'Amount' => $amount,
            'Initiator' => $this->initiator_username,
            'SecurityCredential' => $this->cred,
            'QueueTimeOutURL' => 'https://domain.com',
            'ResultURL' => 'https://domain.com',
            'TransactionID' => 'LIE81C8EFI'
        );
        $data = json_encode($data);
        $url = $this->base_url.'reversal/v1/request';
        $response = $this->submit_request($url, $data);
        return $response;
    }
    
    /*********************************************************************
     * 
     *  LNMO APIs
     * 
     * *******************************************************************/
     
    public function lnmo_request($amount, $phone, $ref = "Payment"){
        if(!is_numeric($amount) || $amount < 10 || !is_numeric($phone)){
            throw new Exception("Invalid amount and/or phone number. Amount should be 10 or more, phone number should be in the format 254xxxxxxxx");
            return FALSE;
        }
        $timestamp = date('YmdHis');
        $passwd = base64_encode($this->lipa_na_mpesa.$this->lipa_na_mpesa_key.$timestamp);
        $data = array(
            'BusinessShortCode' => $this->lipa_na_mpesa,
            'Password' => $passwd,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $this->lipa_na_mpesa,
            'PhoneNumber' => $phone,
            'CallBackURL' => 'https://dev.matrixcyber.co.ke/cb.php',
            'AccountReference' => $ref,
            'TransactionDesc' => 'testing too',
        );
        $data = json_encode($data);
        $url = $this->base_url.'stkpush/v1/processrequest';
        $response = $this->submit_request($url, $data);
        $result = json_decode($response);
        return $result;
    }
    
    private function lnmo_query($checkoutRequestID = null){
        $timestamp = date('YmdHis');
        $passwd = base64_encode($this->lipa_na_mpesa.$this->lipa_na_mpesa_key.$timestamp);
        
        if($checkoutRequestID == null || $checkoutRequestID == ''){
            //throw new Exception("Checkout Request ID cannot be null");
            return FALSE;
        }
        
        $data = array(
            'BusinessShortCode' => $this->lipa_na_mpesa,
            'Password' => $passwd,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestID
        );
        $data = json_encode($data);
        $url = $this->base_url.'stkpushquery/v1/query';
        $response = $this->submit_request($url, $data);
        return $response;
    }
}
