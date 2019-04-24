<?php

namespace App\Http\Controllers\Stock;

use App\Model\Purse;
use App\Model\Supplier;
use App\Model\PursesPayment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
    /**
     * Show all supplier
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function allSupplier()
    {
        $suppliers = Supplier::all();
        return view('user.admin.supplier.all-supplier',[
            'suppliers'         =>          $suppliers
        ]);
    }

    /**
     * Add new supplier
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addSupplier()
    {
        return view('user.admin.supplier.add-supplier');
    }

    /**
     * Edit supplier info
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('user.admin.supplier.edit-supplier',[
            'supplier'          =>          $supplier
        ]);
    }

    public function deleteSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier_on_purses = PursesPayment::where('supplier_id', $id)->first();
        $supplier_on_purses_payment = Purse::where('supplier_id', $id)->first();
        if($supplier_on_purses || $supplier_on_purses_payment){
            return redirect()->back()->with('delete_error','Cannot delete suppiler! This suppiler has been used in purses payment.');
        }else{
            $supplier->delete();
            return redirect()->back()->with('delete_success','Delete suppiler successfully!');
        }
    }

    /**
     * Save supplier
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSupplier(Request $request)
    {
        $supplier = new Supplier();
        $supplier->name = $request->get('name');
        $supplier->email = $request->get('email');
        $supplier->phone = $request->get('phone');
        $supplier->address = $request->get('address');
        $supplier->status = 1;
        $supplier->user_id = auth()->user()->id;
        if($supplier->save()){
            return response()->json('Ok',200);
        }
    }

    /**
     * Update supplier
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSupplier(Request $request,$id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->name = $request->get('name');
        $supplier->email = $request->get('email');
        $supplier->phone = $request->get('phone');
        $supplier->address = $request->get('address');
        $supplier->status = $request->get('status') == 'on' ? 1 : 0;
        $supplier->user_id = auth()->user()->id;
        if($supplier->save()){
            return response()->json('Ok',200);
        }
    }

    /**
     * View supplier view details
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('user.admin.supplier.view-supplier',[
            'supplier'      =>      $supplier
        ]);
    }
}
