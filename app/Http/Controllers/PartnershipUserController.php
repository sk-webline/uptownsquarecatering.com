<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PartnershipUser;
use App\User;
use App\Customer;
use Illuminate\Support\Facades\Hash;
use Mail;
use App\Mail\PartnreshipAcceptMailManager;
use App\Mail\SecondEmailVerifyMailManager;

class PartnershipUserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $sort_search = null;
    $users = PartnershipUser::whereNull('user_id')->orderBy('created_at', 'desc');
    if ($request->has('search')){
      $sort_search = $request->search;
      $users = $users->where('name', 'like', '%'.$sort_search.'%');
    }
    $users = $users->paginate(15);
    return view('backend.customer.partnership.index', compact('users', 'sort_search'));
  }


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('backend.product.stores.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $user = new PartnershipUser;
    $user->name = $request->name;
    $user->company = $request->company;
    $user->email = $request->email;
    $user->country = $request->country;
    $user->phone_code = $request->phone_code;
    $user->phone = $request->phone;
    $user->interests = $request->interests;
    $user->save();

    flash(translate('Partnership User has been inserted successfully'))->success();
    return redirect()->route('partnership-user.index');
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
  public function edit(Request $request, $id)
  {

    $user = PartnershipUser::find($id);

    return view('backend.customer.partnership.edit', compact('user'));
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
    $user = PartnershipUser::find($id);
    $user->name = $request->name;
    $user->company = $request->company;
    $user->email = $request->email;
    $user->country = $request->country;
    $user->phone_code = $request->phone_code;
    $user->phone = $request->phone;
    $user->interests = $request->interests;
    $user->save();

    flash(translate('Partnership User has been updated successfully'))->success();
    return redirect()->route('partnership-user.index');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $part_user = PartnershipUser::find($id);
    $user = User::where('email', $part_user->email)->first();
    $user->partner = 0;
    $user->save();
    PartnershipUser::destroy($id);


    flash(translate('Partnership User has been deleted successfully'))->success();
    return redirect()->route('partnership-user.index');
  }

  public function change_accept(Request $request) {
    $partner_user = PartnershipUser::find($request->id);
    $partner_user->accept = $request->status;
    $partner_user->save();

    $user = User::where('email', $partner_user->email)->first();
    if($user) {
      $user->partner = $request->status;
    }

    if($request->status == 1) {
      if($user) {
        $user->name = $partner_user->name;
        $user->company = $partner_user->company;
        $user->country = $partner_user->country;
        $user->city = $partner_user->city;
        $user->phone_code = $partner_user->phone_code;
        $user->phone = $partner_user->phone;
        $user->interests = $partner_user->interests;
        $array = array(
          'subject' => env('APP_NAME').': Partnership Response',
          'user' => $user,
        );
        $sender = $user->email;
        try {
          Mail::to($sender)->queue(new PartnreshipAcceptMailManager($array));
        } catch (\Exception $e) {
        }
      } else {
        $new_user = new User;
        $password = getUniqueCode(10);
        $new_user->name = $partner_user->name;
        $new_user->company = $partner_user->company;
        $new_user->email = $partner_user->email;
        $new_user->country = $partner_user->country;
        $new_user->city = $partner_user->city;
        $new_user->phone_code = $partner_user->phone_code;
        $new_user->phone = $partner_user->phone;
        $new_user->interests = $partner_user->interests;
        $new_user->password = Hash::make($password);
        $new_user->email_verified_at = date('Y-m-d H:m:s');
        $new_user->partner = $request->status;
        $new_user->save();

        $customer = new Customer;
        $customer->user_id = $new_user->id;
        $customer->save();

        $partner_user->user_id = $new_user->id;
        $partner_user->save();

        $array['view'] = 'emails.verification';
        $array['from'] = env('MAIL_USERNAME');
        $array['subject'] = 'You have been accepted as partner from '.env('APP_NAME');
        $array['content'] = translate('Your password is ').$password;

        try {
          Mail::to($new_user->email)->queue(new SecondEmailVerifyMailManager($array));
        } catch (\Exception $e) {
        }
      }
    }
    else {
        $user->excluded_vat = 0;
    }
    if($user) {
      $user->save();
    }
    return 1;
  }
  public function accept_partner_request(Request $request) {

    $partner_user = PartnershipUser::find($request->id);

    $user = User::where('email', $partner_user->email)->first();
    if($user) {
      $user->partner = $request->status;
    }

    if($request->status == 1) {
      if($user && $user->id == $partner_user->registered_customer) {
        $user->name = $partner_user->name;
        $user->company = $partner_user->company;
        $user->country = $partner_user->country;
        $user->city = $partner_user->city;
        $user->phone_code = $partner_user->phone_code;
        $user->phone = $partner_user->phone;
        $user->interests = $partner_user->interests;
        $user->partner = 1;
        $user->accept_partner_request = 1;
        $user->excluded_vat = 0;

        $partner_user->user_id = $user->id;
        $partner_user->accept = $request->status;
        $partner_user->customer_and_after_partner = 1;
        $partner_user->save();

        $array = array(
          'subject' => env('APP_NAME').': Partnership Response',
          'user' => $user,
        );
        $sender = $user->email;
        try {
          Mail::to($sender)->queue(new PartnreshipAcceptMailManager($array));
        } catch (\Exception $e) {
        }
        return response([
          'status' => true,
          'customer_id' => encrypt($user->customer->id),
        ]);
      } else {
        $new_user = new User;
        $password = getUniqueCode(10);
        $new_user->name = $partner_user->name;
        $new_user->company = $partner_user->company;
        $new_user->email = $partner_user->email;
        $new_user->country = $partner_user->country;
        $new_user->city = $partner_user->city;
        $new_user->phone_code = $partner_user->phone_code;
        $new_user->phone = $partner_user->phone;
        $new_user->interests = $partner_user->interests;
        $new_user->password = Hash::make($password);
        $new_user->email_verified_at = date('Y-m-d H:m:s');
        $new_user->partner = 1;
        $new_user->accept_partner_request = 1;
        $new_user->excluded_vat = 0;
        $new_user->save();

        $customer = new Customer;
        $customer->user_id = $new_user->id;
        $customer->save();

        $partner_user->user_id = $new_user->id;
        $partner_user->accept = $request->status;
        $partner_user->save();

        return response([
          'status' => true,
          'customer_id' => encrypt($customer->id),
        ]);

        /*$array['view'] = 'emails.verification';
        $array['from'] = env('MAIL_USERNAME');
        $array['subject'] = 'You have been accepted as partner from '.env('APP_NAME');
        $array['content'] = translate('Your password is ').$password;

        try {
          Mail::to($new_user->email)->queue(new SecondEmailVerifyMailManager($array));
        } catch (\Exception $e) {
        }*/
      }
      return response([
        'status' => false,
        'user_id' => $user->id,
        'message' => "The email  $partner_user->email belongs to another user"
      ]);
    }

    return 0;
  }
}
