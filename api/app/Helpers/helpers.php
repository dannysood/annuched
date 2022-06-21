<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

function getUserFromFirebaseUid(string $firebaseUid) {
    return Cache::remember(Config::get('constants.cache.users.userFromFirebasePrefix').$firebaseUid, Config::get('constants.cache.users.ttlUserFromFirebase'), function() use(&$firebaseUid){
        return User::where('firebase_uid', $firebaseUid)->firstOrFail();
    });
}


?>
