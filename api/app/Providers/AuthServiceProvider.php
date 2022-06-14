<?php

namespace App\Providers;

use App\Models\User;
use Auth;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // migrate to https://laravel.com/docs/9.x/authentication#adding-custom-guards for better cache management
        // this guard checks if firebase-token is set and if so then tries to login a user using it
        Auth::viaRequest('firebase-token', function (Request $request) {

            $firebaseJWTToken = substr($request->header('Authorization'), 7);
            $firebaseUid = "";
            $name = "";
            $email = "";

            if (Config::get('constants.jwt.firebase.isVerifyToken') == false) {
                // To be used for user seeding and unit testing with expired jwt tokens
                $tks = explode('.', $firebaseJWTToken);
                list($headb64, $bodyb64, $cryptob64) = $tks;
                $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64));
                $firebaseUid = $payload->user_id;
                $email = $payload->email;
                $name = $payload->name;
            } else {
                $auth = Firebase::auth();
                $verifiedIdToken = $auth->verifyIdToken($firebaseJWTToken, true);
                $firebaseUid = $verifiedIdToken->claims()->get('user_id');
                $email = $verifiedIdToken->claims()->get('email');
                $name = $verifiedIdToken->claims()->get('name');
            }

            $user = null;
            // TODO clean this up as this is a dirty way to if-else based on if this route is auth/create
            if (str_contains($request->getPathInfo(), "auth/create")) {
                $request->request->add(['email' => $email, 'name' => $name, 'firebase_uid' => $firebaseUid]);
            } else {
                // TODO use caching to avoid direct db calls to fetch authenticated user
                $user = User::where('firebase_uid', $firebaseUid)->firstOrFail();
            }

            return $user;
        });
    }
}
