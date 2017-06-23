<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;


class PaypalController extends Controller
{

    public
    function __construct ()
    {
        // setup PayPal api context
        $paypal_conf = Config::get ( 'paypal' );
        $this->_api_context = new ApiContext(
            new OAuthTokenCredential( $paypal_conf[ 'client_id' ] ,
                                      $paypal_conf[ 'secret' ] ) );
        $this->_api_context->setConfig ( $paypal_conf[ 'settings' ] );
    }

    public
    function index ()
    {
        return view ( 'paypalform' );
    }

    // payment method for email package subcription
    public
    function payment ( Request $request )
    {
        $input = array_except ( $request->all () , array( '_token' ) );
        //dd($request->get('amount'));
        $payer = new Payer();
        $payer->setPaymentMethod ( 'paypal' );

        $item_1 = new Item();

        $item_1->setName ( 'Item 1' )// item name
        ->setCurrency ( 'USD' )
            ->setQuantity ( 1 )
            ->setPrice ( $request->get ( 'amount' ) ); // unit price

        $item_list = new ItemList();
        $item_list->setItems ( array( $item_1 ) );

        $amount = new Amount();
        $amount->setCurrency ( 'USD' )
            ->setTotal ( $request->get ( 'amount' ) );

        $transaction = new Transaction();
        $transaction->setAmount ( $amount )
            ->setItemList ( $item_list )
            ->setDescription ( 'Your transaction description' );

        $redirects_urls = new RedirectUrls();
        $redirects_urls->setReturnUrl ( route ( 'paypal.status' ) )//specify return URL
        ->setCancelUrl ( route ( 'paypal.status' ) );

        $payment = new Payment();
        $payment->setIntent ( 'Sale' )
            ->setPayer ( $payer )
            ->setRedirectUrls ( $redirects_urls )
            ->setTransactions ( array( $transaction ) );
        // dd($payment->create($this->>_api_context)); exit;
        try {
            $payment->create ( $this->_api_context );
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            if ( \Config::get ( 'app.debug' ) ) {
                Session::put ( 'error' , 'Connection timeout' );
                return Redirect::route ( 'paypal.form' );
                // echo "Exception: " . $ex->getMessage() . PHP_EOL;
                // $err_data = json_decode($ex->getData(), true);
                // exit;
            } else {
                Session::put ( 'error' , 'Some error occur, sorry for inconvenient' );
                return Redirect::route ( 'paypal.form' );
                // die('Some error occur, sorry for inconvenient');
            }
        }

        foreach ( $payment->getLinks () as $link ) {
            if ( $link->getRel () == 'approval_url' ) {
                $redirect_url = $link->getHref ();
                break;
            }
        }

        // add payment ID to session
        Session::put ( 'paypal payment_id' , $payment->getId () );

        if ( isset( $redirect_url ) ) {
            // redirect to paypal
            return Redirect::away ( $redirect_url );
        }

        Session::put ( 'error' , 'Unknown error occurred' );
                return Redirect::route ( 'paypal.form' );
    }

    public function getPaymentStatus ()
    {
        // Get the payment ID before session clear
        $payment_id = Session::get ( 'paypal_payment_id' );
        // clear the session payment ID
        Session::forget ( 'paypal_payment_id' );
        if ( empty( Input::get ( 'PayerID' ) ) || empty( Input::get ( 'token' ) ) ) {
            notificationMsg ( 'error' , 'Payment failed' );
            return Redirect::route ( 'paypal.form' );
        }
        $payment = Payment::get ( $payment_id , $this->_api_context );
        // PaymentExecution object includes information necessary
        // to execute a PayPal account payment.
        // The payer_id is added to the request query parameters
        // when the user is redirected from paypal back to your site
        $execution = new PaymentExecution();
        $execution->setPayerId ( Input::get ( 'PayerID' ) );
        //Execute the payment
        $result = $payment->execute ( $execution , $this->_api_context );

        //echo '<pre>' ; print_r($result); echo '<pre>'; exit; // DEBUG RESULT, remove it later

        if ( $result->getState () == 'approved' ) { // payment made
            Session::put ( 'success' , 'Payment success' );
            return Redirect::route ( 'paypal.form' );
        }

    }
}

//https://www.youtube.com/watch?v=8ArEzyCEKl4&list=PLfdtiltiRHWE_c8jjW5OeweL1c_8uqcnW&index=1


//https://www.youtube.com/watch?v=ly2xm_NM46g

//https://github.com/guzzle/guzzle

//https://github.com/paypal/PayPal-PHP-SDK

//https://github.com/guzzle/guzzle/issues/575

























