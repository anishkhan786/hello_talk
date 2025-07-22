<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MarketingItem;
use Illuminate\Support\Facades\Storage;

class MarketingItemController extends Controller
{
    public function index()
    {
        $data = MarketingItem::orderByDesc('id')->paginate(10);
        return view('admin.marketing_items.index', compact('data'));
    }

    public function create()
    {
        return view('admin.marketing_items.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'price' => 'required|integer',
            'total_click'=> 'required|integer',
            'status' => 'required|in:1,2',
            'file_type' => 'required|in:1,2',
            'attachment' => [
                            'required',
                            'file',
                            'mimetypes:image/jpeg,image/png,image/jpg,video/mp4',
                            'max:10240' // Max 10 MB
                        ]

        ]);

        if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $mime = $file->getMimeType();
                
                if ($request->file_type == '2' AND $mime === 'video/mp4') {
                    $attachmentPath = $request->file('attachment')->store('marketing', 'public');
                } elseif ($request->file_type == '1' AND $mime != 'video/mp4') {
                    $attachmentPath = $request->file('attachment')->store('marketing', 'public');
                } else {
                    if($request->file_type == '2'){
                        return back()->withErrors(['attachment' => 'Please select a valid file type. The video must be 15 seconds or less.'])->withInput();
                    } else {
                        return back()->withErrors(['attachment' => 'Please select a valid file type'])->withInput();
                    }
                }            
        } else {
            return back()->withErrors(['attachment' => 'Attachment required'])->withInput();
        }


        MarketingItem::create([
            'title' => $request->title,
            'url' => $request->url,
            'media_file' => $attachmentPath,
            'clicks' => $request->total_click,
            'price' => $request->price,
            'status' => $request->status,
            'file_type' => $request->file_type,
        ]);

        return redirect()->route('marketing')->with('success', 'Item created.');
        
    }

    public function edit($id)
    {
        $item = MarketingItem::findOrFail($id);
        return view('admin.marketing_items.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = MarketingItem::findOrFail($id);

         $request->validate([
            'title' => 'required|string',
            'price' => 'required|integer',
            'total_click'=> 'required|integer',
            'status' => 'required|in:1,2',
            'file_type' => 'required|in:1,2',

        ]);

          if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $mime = $file->getMimeType();
                
                if ($request->file_type == '2' AND $mime === 'video/mp4') {
                    $attachmentPath = $request->file('attachment')->store('marketing', 'public');
                } elseif ($request->file_type == '1' AND $mime != 'video/mp4') {
                    $attachmentPath = $request->file('attachment')->store('marketing', 'public');
                } else {
                    if($request->file_type == '2'){
                        return back()->withErrors(['attachment' => 'Please select a valid file type. The video must be 15 seconds or less.'])->withInput();
                    } else {
                        return back()->withErrors(['attachment' => 'Please select a valid file type'])->withInput();
                    }
                }            
        }

        $data = array(
            'title' => $request->title,
            'url' => $request->url,
            'clicks' => $request->total_click,
            'price' => $request->price,
            'status' => $request->status,
            'file_type' => $request->file_type,
        );
        if(isset($attachmentPath) AND !empty($attachmentPath)){
            $data['media_file'] = $attachmentPath;
        }

        $item->update($data);

         return redirect()->back()->with('success', 'Item updated.');
    }

    public function destroy($id)
    {
        MarketingItem::destroy($id);
       return redirect()->back()->with('warning','Marketing item deleted.');
    }
}
