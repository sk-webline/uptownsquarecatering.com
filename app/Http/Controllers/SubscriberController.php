<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subscriber;

class SubscriberController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $subscribers = Subscriber::orderBy('created_at', 'desc')->paginate(15);
    return view('backend.marketing.subscribers.index', compact('subscribers'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $request->validate([
      'newsletter_email' => 'required|email',
    ]);

    // MailChimp API credentials
    $apiKey = env('MAILCHIMP_API');
    $listID = env('MAILCHIMP_LIST_ID');

    // MailChimp API URL
    $memberID = md5(strtolower($request->newsletter_email));
    $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/' . $memberID;

    // member information
    $memberjson = json_encode([
      'email_address' => $request->newsletter_email,
      'status'        => 'subscribed',
      'merge_fields'  => [
        'FNAME'     => "",
        'LNAME'     => "",
        'DATESUB'     => date("d/m/Y"),
        'TIMESUB'     => date("H:i"),
        'IP'     => $_SERVER['REMOTE_ADDR'],
        'GDPR'     => 'I agree with the Terms and Policies',
      ]
    ]);

    // send a HTTP POST request with curl
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $memberjson);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $subscriber = Subscriber::where('email', $request->newsletter_email)->first();
    if($subscriber == null){
      $subscriber = new Subscriber;
      $subscriber->email = $request->newsletter_email;
      $subscriber->save();
      flash(translate('You have subscribed successfully'))->success();
    }
    else{
      flash(translate('You are already a subscriber'))->success();
    }
    return back();
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function subscribers_store_ajax(Request $request)
  {
    $request->validate([
      'newsletter_email' => 'required|email',
    ]);

    // MailChimp API credentials
    $apiKey = env('MAILCHIMP_API');
    $listID = env('MAILCHIMP_LIST_ID');

    // MailChimp API URL
    $memberID = md5(strtolower($request->newsletter_email));
    $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/' . $memberID;

    // member information
    $memberjson = json_encode([
      'email_address' => $request->newsletter_email,
      'status'        => 'subscribed',
      'merge_fields'  => [
        'FNAME'     => "",
        'LNAME'     => "",
        'DATESUB'     => date("d/m/Y"),
        'TIMESUB'     => date("H:i"),
        'IP'     => $_SERVER['REMOTE_ADDR'],
        'GDPR'     => 'I agree with the Terms and Policies',
      ]
    ]);

    // send a HTTP POST request with curl
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $memberjson);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $subscriber = Subscriber::where('email', $request->newsletter_email)->first();
    if($subscriber == null){
      $subscriber = new Subscriber;
      $subscriber->email = $request->newsletter_email;
      $subscriber->save();
      return array('status' => 1);
    }
    else{
      return array('status' => 0);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    Subscriber::destroy($id);
    flash(translate('Subscriber has been deleted successfully'))->success();
    return redirect()->route('subscribers.index');
  }
}
