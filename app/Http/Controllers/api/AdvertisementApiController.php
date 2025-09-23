<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\MarketingItem;
use App\Models\MarketingView;
use App\Models\MarketingUserView;
use App\Models\MarketingUserEventLogs;
use App\Models\User;
use App\Models\UserSubscriptions;
use App\Models\HelperLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DB;
use Carbon\Carbon;
class AdvertisementApiController extends Controller
{
   

    public function ads_get(Request $request)
        {
            $user = auth()->user();
            $page_name = $request->page_name;
            $userId = $request->user_id;
            $today = date('Y-m-d');

            $today_date = now();
            $subscription_res = UserSubscriptions::with('plan')
                                ->where('end_date', '>=', $today_date)
                                ->where('user_id', $userId)
                                ->where('payment_status', 'success')
                                ->where('status', 'active')
                                ->first();
            if(!empty($subscription_res)){
                return response()->json([
                    'message' => 'Ads was not applied.',
                    'type' => '',
                    'status' => false,
                    'data' => [],
                ], 200);
            }

            $log_letest_data = MarketingUserEventLogs::where('user_id', $userId)
                        ->where('view_date', $today)
                        ->orderBy('id', 'desc')
                        ->first();
            if(!empty($log_letest_data) AND $log_letest_data->data == 'menuaal'){
                $ads_type = 'meta';
            } else {
                $ads_type = 'menuaal';
            }

            $ads_type = 'menuaal';// only menuaal all time 
            // Handle ads block conditions for different pages
            $eventLogConditions = [
                'landing' => function () use ( $today, $userId, $user) {
                
                    $withinDays = Carbon::parse($user->created_at)->diffInDays(now()) <= env('ads_landing_page_user_view',7);
                    // 2️⃣ Check if ad already shown in last 24 hours
                    $lastShown = MarketingUserEventLogs::where('user_id', $userId)
                        ->where('event_type', 'landing')
                        ->where('view_date', $today) // last 24 hrs
                        ->exists();
                     return $withinDays && !$lastShown;
                },
                'audio_call' => function () use ($userId, $page_name, $today) {
                    return MarketingUserEventLogs::where('event_type', $page_name)
                        ->where('user_id', $userId)
                        ->where('view_date', $today)
                        ->count() === 0;
                },
                 'exercise' => function () use ($userId, $page_name, $today) {
                    return MarketingUserEventLogs::where('event_type', $page_name)
                        ->where('user_id', $userId)
                        ->where('view_date', $today)
                        ->count() === 0;
                },
                'profile_view' => function () use ($userId, $page_name, $today) {
                    return MarketingUserEventLogs::where('event_type', $page_name)
                        ->where('user_id', $userId)
                        ->where('view_date', $today)
                        ->count() < env('ads_check_users_view_per_day');
                },
                'translation' => function () use ($userId, $page_name, $today) {
                    return MarketingUserEventLogs::where('event_type', $page_name)
                        ->where('user_id', $userId)
                        ->where('view_date', $today)
                        ->count() < env('ads_translate_per_day');
                },
            ];


             $withinDays = Carbon::parse($user->created_at)->diffInDays(now()) <= env('ads_landing_page_user_view');
                    // 2️⃣ Check if ad already shown in last 24 hours
                    $lastShown = MarketingUserEventLogs::where('user_id', $userId)
                        ->where('event_type', 'landing')
                        ->where('view_date', $today) // last 24 hrs
                        ->first();
            if($withinDays){
                echo 'if';
                dd($lastShown);
            } else {
                echo 'aa --- '.env('ads_landing_page_user_view');
                dd($lastShown);
            }
            exit();

            if (isset($eventLogConditions[$page_name]) && $eventLogConditions[$page_name]()) {
                // Log the first event only if not already logged
                MarketingUserEventLogs::create([
                    'user_id' => $userId,
                    'event_type' => $page_name,
                    'view_date' => $today,
                    'data' => $ads_type,

                ]);

                return response()->json([
                    'message' => 'Ads was not applied.',
                    'type' => $ads_type,
                    'status' => false,
                    'data' => [],
                ], 200);
            } else {
                if(!empty($log_letest_data) AND $log_letest_data->data == 'meta'){
                    $ads_type = 'menuaal';

                     // Log event
                        MarketingUserEventLogs::create([
                            'user_id' => $userId,
                            'event_type' => $page_name,
                            'view_date' => $today,
                            'data' => 'meta',
                        ]);

                    return response()->json([
                        'message' => 'Ads fetched successfully',
                        'ads_type' => 'meta',
                        'status' => true,
                        'data' => $nextAd ?? [],
                    ], 200);
            
                } else {
                    $ads_type = 'menuaal';
                }
                MarketingUserEventLogs::where('event_type', $page_name)->where('user_id', $userId)->where('view_date', $today)->delete();
            }

            // Get the current view round or default to 1
            $currentRound = MarketingUserView::where('user_id', $userId)->max('view_round') ?? 1;

            // Fetch all active ad IDs
            $allAdIds = MarketingItem::where('status', 1)->pluck('id')->toArray();

            // Fetch already viewed ads in current round
            $viewedAdIds = MarketingUserView::where('user_id', $userId)
                ->where('view_round', $currentRound)
                ->pluck('marketing_item_id')
                ->toArray();

            // Determine unseen ads
            $unseenAdIds = array_diff($allAdIds, $viewedAdIds);

            if (!empty($unseenAdIds)) {
                // Pick the next unseen ad
                $nextAd = MarketingItem::whereIn('id', $unseenAdIds)
                    ->orderBy('id', 'asc')
                    ->first();

                $roundToUse = $currentRound;
            } else {
                // All ads seen, move to next round
                $nextAd = MarketingItem::where('status', 1)
                    ->orderBy('id', 'asc')
                    ->first();

                $roundToUse = $currentRound + 1;
            }

            if (!empty($nextAd)) {
                // Log ad view
                MarketingUserView::create([
                    'user_id' => $userId,
                    'marketing_item_id' => $nextAd->id,
                    'view_round' => $roundToUse,
                    'view_date' => $today,
                ]);

                // Log event
                MarketingUserEventLogs::create([
                    'user_id' => $userId,
                    'event_type' => $page_name,
                    'view_date' => $today,
                    'data' => $ads_type,
                ]);

                // Prepare media URL
                $nextAd->media_file = Storage::disk('s3')->url($nextAd->media_file);
            }

            return response()->json([
                'message' => 'Ads fetched successfully',
                'ads_type' => $ads_type,
                'status' => true,
                'data' => $nextAd ?? [],
            ], 200);
        }


    public function ads_click(Request $request)
        {
            try {
                $ads = MarketingItem::where('status', 1)
                    ->where('id', $request->marketing_item_id)
                    ->first();

                if (!$ads) {
                    return response()->json(['message' => 'Limit is over', 'status' => false], 200);
                }

                $currentCount = MarketingView::where('marketing_item_id', $request->marketing_item_id)->count();

                // Record the click/view
                MarketingView::create([
                    'user_id' => $request->user_id,
                    'marketing_item_id' => $request->marketing_item_id,
                    'view_date' => date('Y-m-d'),
                ]);

                // If new count equals or exceeds max allowed clicks, update ad status
                if ($currentCount + 1 >= $ads->clicks) {
                    $ads->update(['status' => 2]);
                }

                return response()->json(['message' => 'Ads clicked', 'status' => true], 200);

            } catch (\Exception $e) {
                return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }

    
    
}