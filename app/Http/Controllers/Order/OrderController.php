<?php

namespace App\Http\Controllers\Order;

use App\Model\Dish;
use App\Model\Order;
use App\Model\Table;
use App\Model\DishType;
use App\Model\DishPrice;
use App\Events\OrderCancel;
use App\Events\OrderServed;
use App\Events\OrderSubmit;
use App\Events\OrderUpdate;
use App\Model\OrderDetails;
use App\Events\StartCooking;
use App\Model\CookedProduct;
use Illuminate\Http\Request;
use App\Events\CompleteCooking;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Show authenticate waiter order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function newOrder()
    {
        $tables = Table::all();
        $dishes = Dish::all();
        return view('user.waiter.order.add-order', [
            'tables' => $tables,
            'dishes' => $dishes
        ]);
    }

    /**
     * Show all order (used in admin / shop manager)
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function allOrder()
    {
        $orders = Order::where('id','!=',0)
            ->orderBy('id','desc')
            ->get()
            ->groupBy(function ($data){
               return $data->created_at->format('M-Y');
            });

        return view('user.admin.order.all-order',[
            'orders'        =>      $orders
        ]);
    }

    /**
     * Non paid order only view for admin and shop manager
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function nonPaidOrder()
    {
        $orders = Order::where('user_id',0)
            ->orderBy('id','desc')
            ->get();
        return view('user.admin.order.non-paid-order',[
            'orders'    =>  $orders
        ]);
    }

    /**
     * Create new order
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveOrder(Request $request)
    {
        $order = new Order();
        $order->order_no = 25142;
        $order->table_id = $request->get('table_id');
        $order->payment = $request->get('payment');
        $order->vat = $request->get('vat');
        $order->change_amount = $request->get('change');
        $order->served_by = auth()->user()->id;
        if ($order->save()) {
            foreach ($request->get('dishList') as $dish) {
                $orderDetails = new OrderDetails();
                $dishType = DishPrice::findOrFail($dish['dish_type_id']);
                $orderDetails->order_id = $order->id;
                $orderDetails->dish_id = $dish['dish_id'];
                $orderDetails->dish_type_id = $dish['dish_type_id'];
                $orderDetails->quantity = $dish['quantity'];
                $orderDetails->net_price = $dishType->price;
                $orderDetails->gross_price = $dish['quantity'] * $dishType->price;
                if ($orderDetails->save()) {
                    foreach ($dishType->recipes as $recipe) {
                        $cookedProduct = new CookedProduct();
                        $cookedProduct->order_id = $order->id;
                        $cookedProduct->product_id = $recipe->product_id;
                        $cookedProduct->quantity = $recipe->unit_needed * $orderDetails->quantity;
                        $cookedProduct->save();
                    }
                    continue;
                } else {
                    break;
                }
            }
            broadcast(new OrderSubmit($order));
            return response()->json('Ok', 200);
        }

    }

    /**
     * Edit order
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editOrder($id)
    {
        $order = Order::findOrFail($id);
        $dishes = Dish::all();
        return view('user.waiter.order.edit-order',[
            'order'     =>      $order,
            'dishes'    =>      $dishes
        ]);
    }

    /**
     * View order details
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getOrderDetails($id)
    {
        $order = Order::findOrFail($id);
        return $order->orderDetails;
        return response()->json([$order->orderDetails,$order->orderDetails->product]);
    }

    /**
     * Order of authenticate user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function myOrder()
    {
        $orders = Order::where('served_by',auth()->user()->id)->get();
        return view('user.waiter.order.my-order',[
            'orders'        =>      $orders
        ]);
    }

    /**
     * Update order
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request,$id)
    {
        $order = Order::findOrFail($id);
        OrderDetails::where('order_id',$order->id)->delete();
        CookedProduct::where('order_id',$order->id)->delete();
        $order->payment = $request->get('payment');
        $order->change_amount = $request->get('change');
        if($order->save()){
            foreach ($request->get('dishList') as $dish) {
                $orderDetails = new OrderDetails();
                $dishType = DishPrice::findOrFail($dish['dish_type_id']);
                $orderDetails->order_id = $order->id;
                $orderDetails->dish_id = $dish['dish_id'];
                $orderDetails->dish_type_id = $dish['dish_type_id'];
                $orderDetails->quantity = $dish['quantity'];
                $orderDetails->net_price = $dishType->price;
                $orderDetails->gross_price = $dish['quantity'] * $dishType->price;
                if ($orderDetails->save()) {
                    foreach ($dishType->recipes as $recipe) {
                        $cookedProduct = new CookedProduct();
                        $cookedProduct->order_id = $order->id;
                        $cookedProduct->product_id = $recipe->product_id;
                        $cookedProduct->quantity = $recipe->unit_needed * $orderDetails->quantity;
                        $cookedProduct->save();
                    }
                    continue;
                } else {
                    break;
                }
            }
            broadcast(new OrderUpdate('Order Update'))->toOthers();
            return response()->json('Ok',200);
        }

    }

    /**
     * Print order if payment is complete
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function printOrder($id)
    {
        $order = Order::findOrFail($id);
        return view('user.admin.order.print-order',[
            'order'     =>      $order
        ]);
    }

    /**
     * Mark order (if order marked, no one can edit/delete this order)
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->user_id = auth()->user()->id;
        if($order->save()){
            return response()->json('Ok',200);
        }

    }

    /**
     * Delete order
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOrder($id)
    {
        $order = Order::findOrFail($id);
        OrderDetails::where('order_id',$order->id)->delete();
        CookedProduct::where('order_id',$order->id)->delete();
        if($order->delete()){
            broadcast(new OrderCancel('orderCancel'))->toOthers();
            return redirect()->back()->with('delete_success','The order has been deleted successfully');
        }

    }

    /**
     * Show order of authenticate kitchen
     * @return \Illuminate\Http\JsonResponse
     */
    public function kitchenOrderToJSON()
    {
        $orders = Order::where('kitchen_id',0)
            ->orWhere('kitchen_id',auth()->user()->id)
            ->where('status','!=',2)
            ->with('orderDetails')
            ->with('servedBy')
            ->orderBy('id','desc')
            ->get();
        return response()->json($orders);
    }

    /**
     * Kitchen takes the dish to cook
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function kitchenStartCooking($id)
    {

        $order = Order::findOrfail($id);
        if($order->status == 0){
            $order->status = 1;
            $order->kitchen_id = auth()->user()->id;
            $order->save();
        }
        $orders = Order::where('kitchen_id',0)
            ->orWhere('kitchen_id',auth()->user()->id)
            ->where('status','!=',2)
            ->with('orderDetails')
            ->with('servedBy')
            ->orderBy('id','desc')
            ->get();
        broadcast(new StartCooking($order))->toOthers();
        return response()->json($orders);

    }

    /**
     * Kitchen cooked the order
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function kitchenCompleteCooking($id)
    {
        $order = Order::findOrfail($id);
        $order->status = 2;
        $order->save();
        $orders = Order::where('kitchen_id',0)
            ->orWhere('kitchen_id',auth()->user()->id)
            ->where('status','!=',2)
            ->with('orderDetails')
            ->with('servedBy')
            ->orderBy('id','desc')
            ->get();
        broadcast(new CompleteCooking("Complete"))->toOthers();
        return response()->json($orders);
    }

    /**
     * Waiter served the order
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderServed($id)
    {
        $order = Order::findOrFail($id);
        $order->status = 3;
        if($order->save()){
            broadcast(new OrderServed("success"))->toOthers();
            return response()->json('Ok',200);
        }
    }


}
