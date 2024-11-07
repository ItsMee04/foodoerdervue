<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $order = Order::all();
        return response()->json(['success' => true, 'message' => 'Data Ditemukan', 'data' => $order]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'    =>  'required|max:100',
            'table_no'         =>  'required|max:5',
        ]);

        try {
            DB::beginTransaction();

            $data =  $request->only(['customer_name', 'table_no']);
            $data['order_date'] = Carbon::now()->format('Y-m-d');
            $data['order_time'] = Carbon::now()->format('H:i:s');
            $data['status']     = "Ordered";
            $data['total']      = 0;
            $data['user_id']    = Auth::user()->id;
            $data['items']      = $request->items;

            $order = Order::create($data);

            collect($data['items'])->map(function ($item) use ($order) {
                $product = Item::where('id', $item)->first();
                OrderDetail::create([
                    'order_id'  =>  $order->id,
                    'item_id'   =>  $item,
                    'price'     =>  $product->price
                ]);
            });

            $order->total       = $order->sumOrderPrice();
            $order->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }

        return response()->json(['success' => true, 'message' => 'Data Ditambahkan', 'data' => $order]);
    }

    public function OrderDetail($id)
    {
        $order  = Order::findOrFail($id);

        return response()->json(['success' => true, 'message' => 'Data Ditambahkan', 'data' => $order->loadMissing(['orderDetail:order_id,price', 'OrderDetail.item:id,name,image', 'user:id,name', 'User.role:id,name'])]);
    }

    public function setAsDone($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status != "Ordered") {
            return response()->json(['errors' => true, 'message', 'you cannot set done because status is not ORDERED']);
        }

        $order->status = 'Done';
        $order->save();

        return response()->json(['success' => true, 'message' => 'Pesanan Sudah Done', 'data' => $order]);
    }

    public function payOder($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status != "Done") {
            return response()->json(['errors' => true, 'message', 'you cannot finish order because status is not Done']);
        }

        $order->status = 'Paid';
        $order->save();

        return response()->json(['success' => true, 'message' => 'Order paid successfully', 'data' => $order]);
    }
}
