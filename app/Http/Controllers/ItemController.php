<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'          =>  'required|max:100',
            'price'         =>  'required|integer',
            'image_file'    =>  'nullable|mimes:png,jpg',
        ]);

        if ($request->file('image_file')) {
            $file = $request->file('image_file');
            $fileName = $file->getClientOriginalName();
            $newFileName = Carbon::now()->format("Y-m-d") . " " . $fileName;
            $path = Storage::putFileAs('items', $file, $newFileName);

            $request['image'] = $newFileName;
        }
        $item = Item::create($request->all());
        unset($item->created_at);
        unset($item->updated_at);
        unset($item->deleted_at);

        return response()->json(['success' => true, 'message' => 'Data Ditambahkan', 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          =>  'required|max:100',
            'price'         =>  'required|integer',
            'image_file'    =>  'nullable|mimes:png,jpg',
        ]);

        $item = Item::findOrFail($id);

        if ($request->hasFile('image_file')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }

            $file = $request->file('image_file');
            $fileName = $file->getClientOriginalName();
            $newFileName = Carbon::now()->format("Y-m-d") . " " . $fileName;
            $path = Storage::putFileAs('items', $file, $newFileName);

            $request['image'] = $newFileName;
        }

        $item->update($request->all());
        unset($item->created_at);
        unset($item->updated_at);
        unset($item->deleted_at);

        return response()->json(['success' => true, 'message' => 'Data Diupdate', 'data' => $item]);
    }
}
