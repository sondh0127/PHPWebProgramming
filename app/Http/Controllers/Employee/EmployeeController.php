<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\SaveEmployee;
use App\Http\Requests\Employee\UpdateEmployee;
use App\Model\Dish;
use App\Model\Employee;
use App\Model\Order;
use App\Model\Product;
use App\Model\ProductType;
use App\Model\Purse;
use App\Model\PursesPayment;
// use App\Model\Stock;
use App\Model\Recipe;
use App\Model\Supplier;
use App\Model\Table;
use App\Model\Unit;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

// use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Add new employee
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addEmployee()
    {
        return view('user.admin.employee.add-employee');
    }

    /**
     * View all employee
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function allEmployees()
    {
        $employees = Employee::all();
        foreach ($employees as &$employee) {
            $employee->image = $employee->user->image ? Storage::disk('s3')->url($employee->user->image) : '';
        }
        return view('user.admin.employee.all-employees', [
            'employees' => $employees,
        ]);
    }

    /**
     * Edit employee
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->image = $employee->user->image ? Storage::disk('s3')->url($employee->user->image) : '';
        return view('user.admin.employee.edit-employee', [
            'employee' => $employee,
        ]);
    }

    /**
     * Delete employee
     * @param $id
     */
    public function deleteEmployee($id)
    {
        $user = User::findOrFail($id);
        $user_in_order = Order::where('user_id', $user->id)
            ->orWhere('served_by', $user->id)
            ->orWhere('kitchen_id', $user->id)
            ->first();
        $user_in_dish = Dish::where('user_id', $id)->first();
        $user_id_product = Product::where('user_id', $id)->first();
        $user_in_product_type = ProductType::where('user_id', $id)->first();
        $user_in_purses = Purse::where('user_id', $id)->first();
        $user_in_purses_payment = PursesPayment::where('user_id', $id)->first();
        $user_in_recipe = Recipe::where('user_id', $id)->first();
        // $user_in_stock = Stock::where('user_id',$id)->first();
        $user_in_supplier = Supplier::where('user_id', $id)->first();
        $user_in_table = Table::where('user_id', $id)->first();
        $user_in_unit = Unit::where('user_id', $id)->first();
        $user_in_employee = Employee::where('user_id', $id)->first();

        if ($user_in_order || $user_in_dish || $user_id_product || $user_in_product_type || $user_in_purses
            || $user_in_purses_payment || $user_in_recipe || $user_in_supplier || $user_in_table
            || $user_in_unit
        ) {
            return redirect()->back()->with('delete_error', 'You cannot delete this user');
        } else {
            if ($user->role != 1) {
                Employee::destroy($user->employee->id);
                User::destroy($id);
                return redirect()->back()->with('delete_success', 'Employee has been deleted successfully');
            }
        }

    }

    /**
     * Save employee
     * @param SaveEmployee $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveEmployee(SaveEmployee $request)
    {

        $user = new User();
        $user->name = $request->get('name');
        $user->password = Hash::make($request->get('password'));
        $user->email = $request->get('email');
        $user->role = $request->get('role');
        if ($request->hasFile('thumbnail')) {
            // exception here
            // $user->image = $request->file('thumbnail')
            //     ->move('uploads/employee/', rand(100000, 900000) . '.' . $request->thumbnail->extension());
            $image = $request->file('thumbnail');
            $imageFileName = time() . '.' . $image->getClientOriginalExtension();
            $filePath = 'employee/' . $imageFileName;
            Storage::disk('s3')->put($filePath, file_get_contents($image), 'public');
            $user->image = $filePath;
        }
        if ($user->save()) {
            $employee = new Employee();
            $employee->name = $user->name;
            $employee->phone = $request->get('phone');
            $employee->email = $user->email;
            $employee->address = $request->get('address');
            $employee->user_id = $user->id;
            if ($employee->save()) {
                // exception here
                // Mail::to($user->email)->send(new EmployeRegister($user->email,$request->get('password')));
                return response()->json('Ok', 200);
            }
        }
    }

    /**
     * Update employee
     * @param UpdateEmployee $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEmployee(UpdateEmployee $request, $id)
    {
        $this->validate($request, [
            'email' => Rule::unique('employees')->ignore($id, 'id'),
            // 'email'   =>   Rule::unique('users')->ignore($id + 1, 'id'),
        ]);

        $employee = Employee::findOrFail($id);
        $employee->name = $request->get('name');
        $employee->phone = $request->get('phone');
        $employee->email = $request->get('email');
        $employee->address = $request->get('address');
        if ($employee->save()) {
            $user = User::find($employee->user->id);
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->active = $request->get('status') == 'on' ? 1 : 0;
            $user->role = $request->get('role');
            if ($request->get('password') != "") {
                $user->password = Hash::make($request->get('password'));
            }
            if ($request->hasFile('thumbnail')) {
                // $user->image = $request->file('thumbnail')
                //     ->move('uploads/employee', rand(100000, 900000) . '.' . $request->thumbnail->extension());

                $image = $request->file('thumbnail');
                $imageFileName = time() . '.' . $image->getClientOriginalExtension();
                $filePath = 'employee/' . $imageFileName;
                Storage::disk('s3')->put($filePath, file_get_contents($image), 'public');
                $user->image = $filePath;
            }
            if ($user->save()) {
                // Mail::to($user->email)->send(new EmployeRegister($user->email,$request->get('password')));
                return response()->json('Ok', 200);
            }
        }
    }
}
