<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\Employee\SaveEmployee;
use App\Http\Requests\Employee\UpdateEmployee;
use App\Model\Dish;
use App\Model\Order;
use App\Model\Product;
use App\Model\ProductType;
use App\Model\Purse;
use App\Model\PursesPayment;
use App\Model\Recipe;
use App\Model\Supplier;
use App\Model\Table;
use App\Model\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * View user profile
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileInfo()
    {
        return view('user.profile.profile');
    }

    /**
     * View user information edit page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileEdit()
    {
        $url = auth()->user()->image != "" | null ?
            Storage::disk('s3')->url(auth()->user()->image) : asset('img_assets/avatar.png');
        return view('user.profile.edit-profile', [
            'url' => $url,
        ]);
    }

    /**
     * Change user password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        if (Hash::check($request->get('current_password'), auth()->user()->password)) {
            $user = User::find(auth()->user()->id);
            $user->password = Hash::make($request->get('new_password'));
            if ($user->save()) {
                return response()->json('Ok', 200);
            }
        } else {
            return response()->json('Ok', 500);
        }
    }

    /**
     * Update user profile
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileUpdate(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id);
        $user->email = $request->get('email');
        $user->name = $request->get('name');
        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $imageFileName = time() . '.' . $image->getClientOriginalExtension();
            $filePath = 'employee/' . $imageFileName;
            Storage::disk('s3')->put($filePath, file_get_contents($image), 'public');
            $user->image = $filePath;
        }
        if ($user->save() && $user->role != 1) {
            $employee = User::where('user_id', $user->id)->first();
            $employee->name = $user->name;
            $employee->phone = $request->get('phone');
            $employee->email = $user->email;
            $employee->address = $request->get('address');
            if ($employee->save()) {
                return response()->json('Ok', 200);
            }
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = User::all()->except(User::first()->id);
        return view('user.admin.employee.all-employees', [
            'employees' => $employees,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.admin.employee.add-employee');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new User($request->all());
        $user->password = Hash::make($request->password);

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $imageFileName = time() . '.' . $image->getClientOriginalExtension();
            $filePath = 'employee/' . $imageFileName;
            Storage::disk('s3')->put($filePath, file_get_contents($image), 'public');
            $user->image = $filePath;
        }
        if ($user->save()) {
            // Mail::to($user->email)->send(new UserRegisterMail($user->email,$request->get('password')));
            return response()->json('Ok', 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(User $employee)
    {
        return view('user.admin.employee.edit-employee', [
            'employee' => $employee,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $employee)
    {
        return response()->json('OK', 200);
        $this->validate($request, [
            'email' => Rule::unique('users')->ignore($employee->id, 'id'),
        ]);
        $employee->fill($request->all());
        $employee->active = $request->status == 'on' ? 1 : 0;

        if ($request->password != "") {
            $employee->password = Hash::make($request->password);
        }

        if ($request->hasFile('thumbnail')) {
            Storage::disk('s3')->delete($employee->image);
            $image = $request->file('thumbnail');
            $imageFileName = time() . '.' . $image->getClientOriginalExtension();
            $filePath = 'employee/' . $imageFileName;
            Storage::disk('s3')->put($filePath, file_get_contents($image), 'public');
            $employee->image = $filePath;
        }

        if ($employee->save()) {
            // Mail::to($user->email)->send(new UserRegisterMail($user->email,$request->get('password')));
            return response()->json('Ok', 200);
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
            'email' => Rule::unique('users')->ignore($id, 'id'),
        ]);
        $data = $request->all();
        $employee = User::findOrFail($id);
        $employee->fill($data);
        $employee->active = $request->get('status') == 'on' ? 1 : 0;
        if ($request->get('password') != "") {
            $employee->password = Hash::make($request->get('password'));
        }
        if ($request->hasFile('thumbnail')) {
            Storage::disk('s3')->delete($employee->image);
            $image = $request->file('thumbnail');
            $imageFileName = time() . '.' . $image->getClientOriginalExtension();
            $filePath = 'employee/' . $imageFileName;
            Storage::disk('s3')->put($filePath, file_get_contents($image), 'public');
            $employee->image = $filePath;
        }
        if ($employee->save()) {
            // Mail::to($user->email)->send(new UserRegisterMail($user->email,$request->get('password')));
            return response()->json('Ok', 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $employee)
    {
        $id = $employee->id;
        $user_in_order = Order::where('user_id', $id)
            ->orWhere('served_by', $id)
            ->orWhere('kitchen_id', $id)
            ->first();
        $user_in_dish = Dish::where('user_id', $id)->first();
        $user_id_product = Product::where('user_id', $id)->first();
        $user_in_product_type = ProductType::where('user_id', $id)->first();
        $user_in_purses = Purse::where('user_id', $id)->first();
        $user_in_purses_payment = PursesPayment::where('user_id', $id)->first();
        $user_in_recipe = Recipe::where('user_id', $id)->first();
        $user_in_supplier = Supplier::where('user_id', $id)->first();
        $user_in_table = Table::where('user_id', $id)->first();
        $user_in_unit = Unit::where('user_id', $id)->first();

        if (
            $user_in_order || $user_in_dish || $user_id_product || $user_in_product_type || $user_in_purses
            || $user_in_purses_payment || $user_in_recipe || $user_in_supplier || $user_in_table
            || $user_in_unit
        ) {
            return redirect()->back()->with('delete_error', 'You cannot delete this user');
        } else {
            if ($employee->role != 1) {
                User::destroy($id);
                return redirect()->back()->with('delete_success', 'Employee has been deleted successfully');
            }
        }
    }
}
