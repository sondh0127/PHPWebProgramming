<?php

namespace App\Http\Controllers\Dish;

use App\Model\DishType;
use App\Model\DishPrice;
use App\Model\OrderDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DishTypeController extends Controller
{
    /**
     * Add Dish type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addDishType()
    {
        return view('user.admin.dish-settings.add-dish-type');
    }

    /**
     * Show all dish type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function allDishType()
    {
        $dish_types = DishType::all();
        return view('user.admin.dish-settings.all-dish-type',[
            'dish_types'    =>      $dish_types
        ]);
    }

    /**
     * Show edit page of dish type
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editDishType($id)
    {
        $dish_type = DishType::findOrFail($id);
        return view('user.admin.dish-settings.edit-dish-type',[
            'dish_type'    =>      $dish_type
        ]);
    }

    /**
     * Delete dish type
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteDishType($id)
    {
        $dishType = DishPrice::findOrFail($id);
        $dish_type_on_order = OrderDetails::where('dish_type_id',$id)->first();
        if(!$dish_type_on_order){
            $dishType->delete();
            return redirect()->back()->with('delete_success','Dish type has been deleted successfully');
        }else{
            return redirect()->back()->with('delete_error','You cannot delete this type! this type has been used in dish order');
        }
    }

    /**
     * Save dish type
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDishType(Request $request)
    {
        $dish_type = new DishType();
        $dish_type->dish = $request->get('dish');
        $dish_type->status = 1;
        $dish_type->user_id = auth()->user()->id;
        if($dish_type->save()){
            return response()->json('Ok',200);
        }
    }

    /**
     * Update dish type
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDishType(Request $request,$id)
    {
        $dish_type = DishType::findOrFail($id);
        $dish_type->dish = $request->get('dish');
        $dish_type->status = $request->get('status') == 'on' ? 1 : 0;
        $dish_type->user_id = auth()->user()->id;
        if($dish_type->save()){
            return response()->json('Ok',200);
        }
    }
}
