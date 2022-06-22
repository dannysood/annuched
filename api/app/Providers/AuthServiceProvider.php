<?php

namespace App\Providers;

use App\Models\User;
use Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Throwable;
use Validator;

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
     * bypass firebase auth header validation for authenticating or creating users for
     * user seeding, feature testing and load testing
     */
    private function fakeUserAuth(Request $request)
    {
        $firebaseUid = $request->header('firebase-uid-for-testing');
                Validator::make(['firebase-uid-for-testing' => $firebaseUid], [
                    'firebase-uid-for-testing' => 'required|string|size:28',
                ])->validate();
                $isCreateUserRequest = str_contains($request->getPathInfo(), "auth/create");
                // To be used for user seeding, feature and load testing
                if ($isCreateUserRequest) {
                    $faker = Faker::create();
                    $email = $faker->unique()->safeEmail();
                    $name = Str::random(28);
                    $request->request->add(['email' => $email, 'name' => $name, 'firebase_uid' => $firebaseUid]);
                    return null;
                } else {
                    return getUserFromFirebaseUid($firebaseUid);
                }
    }
    /**
     * validate firebase auth header and either authenticate user or
     * create request elements for create user
     */
    private function realUserAuth(Request $request)
    {
        // real user registration to be used in live environments
        $auth = Firebase::auth();
        $firebaseJWTToken = substr($request->header('Authorization'), 7);
        $verifiedIdToken = $auth->verifyIdToken($firebaseJWTToken, true);
        $firebaseUid = $verifiedIdToken->claims()->get('user_id');
        $isCreateUserRequest = str_contains($request->getPathInfo(), "auth/create");
        if ($isCreateUserRequest) {
            $firebaseUid = $verifiedIdToken->claims()->get('user_id');
            $email = $verifiedIdToken->claims()->get('email');
            $name = $verifiedIdToken->claims()->get('name');
            $request->request->add(['email' => $email, 'name' => $name, 'firebase_uid' => $firebaseUid]);
            return null;

        } else {
            return getUserFromFirebaseUid($firebaseUid);
        }
    }


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

            //
            // TODO currently auth skipped enitrely for jobs, figure out a better way to do the same
            if (str_contains($request->getPathInfo(), "job/fetch-from-remote-blog-api")) {
                return null;
            }

            if (Config::get('constants.jwt.firebase.isVerifyToken') == false) {
                return $this->fakeUserAuth($request);
            } else {
                return $this->realUserAuth($request);
            }

        });
    }
}
