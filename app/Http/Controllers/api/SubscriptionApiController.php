<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\UserSubscriptions;
use App\Models\SubscriptionPrivileges;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanPrivileges;
use App\Models\AppNotification;
use App\Models\Currencies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HelperLanguage;
use DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionApiController extends Controller
{
    public function get_plans(Request $request)
    {
        try {
            $country_code = $request->country_code??'';
            $currencie_data = Currencies::where('country_code', $country_code)->first();

            if(empty($currencie_data)){
                return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_CURRENCY_NOT_FOUND') ?? 'The requested currency is not available.',
                    'status' => false,
                ], 400);
            }

            $datas = SubscriptionPlan::where('status','1')->get();
            $data = $datas->map(function ($data) use($currencie_data) {
                $price = $data->price + $currencie_data->base_price??'0';
                $discounted_price = $data->discounted_price + $currencie_data->base_price??'0';

                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'duration_type' => $data->duration_type,
                    'duration_value' => $data->duration_value,
                    'symbol' => $currencie_data->symbol,
                    'currency' => $currencie_data->currency_code,
                    'price' => $price,
                    'discounted_price' => $discounted_price,
                ];
            });

            $response = ['message'=> 'success','data'=>$data, 'status'=>200];
            return response($response, 200);
        } catch(\Exception $e)  {
           return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function subscription_features(Request $request)
    {
        try {
            $data = SubscriptionPrivileges::get();
            $response = ['message'=> 'success','data'=>$data, 'status'=>200];
            return response($response, 200);
        } catch(\Exception $e)  {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function getSubscriptionPlanFeatures(Request $request)
    {
        try {
            $plan = SubscriptionPlan::where('id', $request->plan_id)->first();
            if (!$plan) {
                return response()->json(['status'=>false, 'error' => 'Plan not found'], 400);
            }

            $privileges = DB::table('subscription_plan_privileges as pp')
                        ->join('subscription_privileges as p', 'pp.privilege_id', '=', 'p.id')
                        ->where('pp.plan_id', $request->plan_id)
                        ->select(
                            'p.id',
                            'p.name',
                            'p.code',
                            'pp.access_type',
                            'pp.limit_value'
                        )
                        ->get();
            
            $response = ['message'=> 'success','status'=>200,
                        'plan_data' => $plan,
                        'star_features' => $privileges
                    ];
            return response($response, 200);
        } catch(\Exception $e)  {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function user_subscription_submit(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'plan_id' => 'required|integer',
            'payment_method' => 'required|string',
            'transaction_id' => 'required|string',
            'amount' => 'required|numeric',
            'payment_status' => 'required',

        ]);

        // Step 2: If basic validation fails
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
        
        // Get plan
        $plan = SubscriptionPlan::where('id', $request->plan_id)->first();
        if (!$plan) {
            return response()->json(['error' => 'Invalid plan'], 400);
        }

        // Calculate end date
        $startDate = now();
        if ($plan->duration_type == 'day') {
            $endDate = $startDate->copy()->addDays($plan->duration_value);
        } elseif ($plan->duration_type == 'month') {
            $endDate = $startDate->copy()->addMonths($plan->duration_value);
        } elseif ($plan->duration_type == 'year') {
            $endDate = $startDate->copy()->addYears($plan->duration_value);
        }

        // Insert subscription

        $subscriptionId = UserSubscriptions::create([
            'user_id'        => $request->user_id,
            'plan_id'        => $plan->id,
            'start_date'     => $startDate,
            'end_date'       => $endDate,
            'amount'         => $request->amount,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'country_code'=> $request->country_code,
            'transaction_id' => $request->transaction_id,
            'status'         => 'active'
        ]);
        $title = HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_welcome_to_premium') ??'Welcome to Premium 🚀';
        $body = HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_subscription_activated') ??'Your subscription is now active. Enjoy all premium features!';

         AppNotification::create([
                'user_id' => $request->user_id,
                'type' => 'in_app',
                'title' =>  $title,
                'body' => $body,
                'channel' => 'in_app',
                'data' =>$subscriptionId,
            ]);

        return response()->json([
            'success' => true,
            'subscription_id' => $subscriptionId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ], 200);
    }

}