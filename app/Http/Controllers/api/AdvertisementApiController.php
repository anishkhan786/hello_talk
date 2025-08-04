<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\MarketingItem;
use App\Models\MarketingView;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DB;
class AdvertisementApiController extends Controller
{
    public function ads_get(Request $request)
    {
        $user = auth()->user();
        $perPage =  $request->per_page??1; // You can change this as needed
        $ads = MarketingItem::where('status', '1')->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Format posts
        $formattedPosts = $ads->getCollection()->map(function ($ads) {
        $media_url = asset('storage/app/public/' . $ads->media_file);;
            return [
                'id' => $ads->id,
                'title' => $ads->title,
                'click_url' => $ads->url,
                'media_file' =>  $media_url,
                'type'=>$ads->file_type?'video':'image'
            ];
        });

        return response()->json([
            'message' => 'Ads fetched successfully',
            'status' => true,
            'data' => [
                'posts' => $formattedPosts,
                'current_page' => $ads->currentPage(),
                'last_page' => $ads->lastPage(),
                'per_page' => $ads->perPage(),
                'total' => $ads->total(),
                'has_more' => $ads->currentPage() < $ads->lastPage()
            ]
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

                return response()->json(['message' => 'Ad clicked', 'status' => true], 200);

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Some internal error occurred.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }

    
    
}