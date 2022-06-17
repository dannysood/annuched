<?php

namespace App\Providers;

use App\Models\User;
use Auth;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
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




            if (Config::get('constants.jwt.firebase.isVerifyToken') == false) {
                // To be used for user seeding and unit testing with expired jwt tokens

                $faker = Faker::create();
                $firebaseUid = $faker->name();
                $email = $faker->unique()->safeEmail();
                $name = Str::random(28);
            } else {
                $firebaseJWTToken = substr($request->header('Authorization'), 7);
                $auth = Firebase::auth();
                $verifiedIdToken = $auth->verifyIdToken($firebaseJWTToken, true);
                $firebaseUid = $verifiedIdToken->claims()->get('user_id');
                $email = $verifiedIdToken->claims()->get('email');
                $name = $verifiedIdToken->claims()->get('name');
            }

            $user = null;
            $request->request->add(['email' => $email, 'name' => $name, 'firebase_uid' => $firebaseUid]);
            // TODO clean this up as this is a dirty way to if-else based on if this route is auth/create
            if (!str_contains($request->getPathInfo(), "auth/create")) {
                $user = getAuthenticatedUserFromFirebaseUid($firebaseUid);
            }
            return $user;
        });
    }
}
