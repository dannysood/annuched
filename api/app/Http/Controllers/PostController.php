<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // cursor pagination provides better performance than paginate (which uses offset) for indexed coulmn
        // https://laravel.com/docs/9.x/pagination#cursor-vs-offset-pagination
        return Post::with(['owner' => function ($query) {
            $query->select('id','name');
        }])->orderBy('created_at', "desc")->cursorPaginate(2);
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

        return Post::create($request->only(['title', 'description', 'owner_id']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return $post->load(['owner' => function ($query) {
            $query->select('id','name');
        }]);
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
