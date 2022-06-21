<?php

namespace App\Http\Controllers\V1;


use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;

class ImportPostJobController extends Controller
{

    /**
     * Store a newly created resource in storage.
     * @param  \App\Http\Requests\StorePostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function getPosts()
    {
        // TODO the job isnt idempotent because there is no unique identifier per post to identify if a post has been copied already
        // Work with API providers to establish an unique identifier to establish idempotency
        try {
            $user = User::where('email',env('ADMIN_EMAIL', 'something@example.com'))->firstOrFail();
        }
        catch(Exception $e){
            print('admin user not found');
            throw $e;
        }

        try {
            $response = Http::retry(3, 100)->get('https://team.javelinhq.com/api/posts');
        }
        catch(Exception $e){
            print('api request failed');
            throw $e;
        }


        $result = $response->json()['data'];
        if(count($result) > 0){
            foreach ($result as $post) {
                // TODO fix uuid auto generate issue in Post::insert to eable bulk import
                // $posts = array();
                // foreach ($result as $post) {
                //     $posts[] = ['title'=> $post['title'],'description' => $post['body'], 'owner_id' => $user->id,'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                // }
                // print(json_encode($posts));
                // Post::insert($posts);

                Post::create(['title'=> $post['title'],'description' => $post['body'], 'owner_id' => $user->id]);
              }
        }
        return response(["message" => "success"], 201);
    }
}
