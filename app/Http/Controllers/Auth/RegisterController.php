<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Invitation;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/backstage/concerts';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
    }

    protected function create(array $data)
    {
        $invitation = Invitation::findByCode($data['invitation_code']);

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $invitation->update(['user_id' => $user->id]);

        auth()->login($user);

        return $user;
    }
}
