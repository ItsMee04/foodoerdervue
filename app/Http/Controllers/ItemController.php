<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ItemController extends Controller
{
    public function generateRandomCode()
    {
        $length = 10;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomCode = '';

        for ($i = 0; $i < $length; $i++) {
            $randomCode .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomCode;
    }

    public function index()
    {
        $item = Item::all();
        unset($item->created_at);
        unset($item->updated_at);
        unset($item->deleted_at);

        if ($item->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Data not found']);
        } else {
            return response()->json(['success' => true, 'message' => 'Data successfully found', 'data' => $item]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          =>  'required|max:100',
            'price'         =>  'required|integer',
            'image_file'    =>  'nullable|mimes:png,jpg',
        ]);

        $randomCode = $this->generateRandomCode();

        if ($request->file('image_file')) {
            $extension = $request->file('image_file')->getClientOriginalExtension();
            $fileName = $randomCode . '.' . $extension;
            $request->file('image_file')->storeAs('items', $fileName);
            $request['image'] = $fileName;
        }
        $item = Item::create($request->all());
        unset($item->created_at);
        unset($item->updated_at);
        unset($item->deleted_at);

        return response()->json(['success' => true, 'message' => 'Data added successfully', 'data' => $item]);
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);

        return response()->json(['success' => true, 'message' => 'Data added successfully', 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          =>  'required|max:100',
            'price'         =>  'required|integer',
            'image_file'    =>  'nullable|mimes:png,jpg',
        ]);

        $item = Item::findOrFail($id);

        $randomCode = $this->generateRandomCode();

        if ($request->hasFile('image_file')) {
            $path     = 'storage/items/' . $item->image;

            if (File::exists($path)) {
                File::delete($path);
            }

            $extension = $request->file('image_file')->getClientOriginalExtension();
            $fileName = $randomCode . '.' . $extension;
            $request->file('image_file')->storeAs('items', $fileName);
            $request['image'] = $fileName;
        }

        $item->update($request->all());
        unset($item->created_at);
        unset($item->updated_at);
        unset($item->deleted_at);

        return response()->json(['success' => true, 'message' => 'Data updated successfully', 'data' => $item]);
    }

    public function delete($id)
    {
        $item = Item::findOrFail($id);

        $path     = 'storage/items/' . $item->image;

        if (File::exists($path)) {
            File::delete($path);
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Data deleted successfully']);
    }
}
