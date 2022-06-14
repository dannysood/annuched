<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['create']]);
        // $this->middleware('auth:api', []);
    }

    /**
     * Create a user via firebase JWT
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateUserRequest $request)
    {
        // TODO cache users after creation so that Users can be pulled from cache remember when needed
        $user = new User();
        $user->firebase_uid = $request->get('firebase_uid');
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->email_verified_at = now();
        // TODO delete password and remember tokens fields from user model as our app doesnt store any password based authentication mechanism
        $user->password = Hash::make(Str::random(30));
        $user->remember_token = Str::random(30);
        $user->save();
        return $user;
    }
}
