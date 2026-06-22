<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Patient;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator($data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:filter', 'max:255', 'unique:users'],
            'phone' => ['required'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.min' => 'The password must be at least 6 characters.',
        ]);
    }

    protected function create($data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['department_id'] = Department::whereName('Patient')->first()->id;
        $data['status'] = 1;

        $user = User::create($data);
        $patient = Patient::create(['user_id' => $user->id]);

        $user->update(['owner_id' => $patient->id, 'owner_type' => Patient::class]);
        $user->assignRole($data['department_id']);

        return $user;
    }
}
