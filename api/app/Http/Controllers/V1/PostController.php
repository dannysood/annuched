<?php

namespace App\Http\Controllers\V1;

use App\Events\PostCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\V1\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // cursor pagination provides better performance than paginate (which uses offset) for indexed column
        // https://laravel.com/docs/9.x/pagination#cursor-vs-offset-pagination
        $cacheKey = $request->input('cursor') != null ? Config::get('constants.cache.keys.posts.paginationPrefix').$request->input('cursor') : Config::get('constants.cache.keys.posts.paginationPrefixRoot');
        return Cache::remember($cacheKey, Config::get('constants.cache.keys.posts.ttl'), function(){
            return PostResource::collection(Post::with(['owner'])->orderBy('created_at', "desc")->cursorPaginate(Config::get('constants.pageSize')));
        });

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request)
    {
        // TODO temporary way to feed an owner id. Remove and use authorized user before golive.
        $userid = User::latest()->first()->id;
        $request->merge(['owner_id' => $userid]);
        $post = Post::create($request->only(['title', 'description', 'owner_id']));
        Event::dispatch(new PostCreated());
        return Cache::remember(Config::get('constants.cache.keys.posts.singleItemPrefix').$post->id, Config::get('constants.cache.keys.posts.ttl'), function() use(&$post){
            // load owner details weven though the owner is the only one who can create so that the newly created post is immidiately available as a cached response
            return new PostResource($post->load(['owner']));
        });

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {

        return Cache::remember(Config::get('constants.cache.keys.posts.singleItemPrefix').$post->id, Config::get('constants.cache.keys.posts.ttl'), function() use(&$post){
            return new PostResource($post->load(['owner']));
        });


    }

    /**
     * Based on business requirements updation and deletion of posts is not allowed.
     * This route should never be successfully hit bacause 405 error is explicitly thrown.
     *
     * @param  \App\Http\Requests\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        return response(['message' => 'Updating posts isnt allowed'], 405);
    }

    /**
     * Based on business requirements updation and deletion of posts is not allowed.
     * This route should never be successfully hit bacause 405 error is explicitly thrown.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        return response(['message' => 'Deleting posts isnt allowed'], 405);
    }
}
