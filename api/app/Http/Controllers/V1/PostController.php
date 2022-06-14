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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     * For first page index checks in order main cache -> db and caches in main cache if needed before returning
     * For rest of pages index checks in order file cache -> main cache -> db and caches in main cache and file cache if needed before returning
     * Index also takes care of file expired cache deletion on demand as its not done automatically
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // cursor pagination provides better performance than paginate (which uses offset) as you dont need to invalidate caching and let it expire via ttl
        // https://laravel.com/docs/9.x/pagination#cursor-vs-offset-pagination
        $isFirstPage = $request->get('cursor') == null;
        $cacheKey = $isFirstPage ? Config::get('constants.cache.posts.paginationPrefixRoot') : Config::get('constants.cache.posts.paginationPrefix').$request->get('cursor');
        $getQueryResultFromMainCacheOrDb = function(string $cacheKey) {
            return Cache::remember($cacheKey, Config::get('constants.cache.posts.ttlMain'), function(){
                return PostResource::collection(Post::with(['owner'])->orderBy('created_at', 'desc')->cursorPaginate(Config::get('constants.pageSize')));
            });
        };
        if($isFirstPage){
            return $getQueryResultFromMainCacheOrDb($cacheKey);
        }

        // TODO installed via self owned fork, figure out maintainence strategy to track remote
        // https://github.com/dannysood/laravel-clear-expired-cache-file
        // remove expired cache keys from file cache before fetching details
        Artisan::call('cache:clear-expired');
        // use local file cache store to avoid repetitive load on main cache for non root page via cursor pagination
        return Cache::store('file')->remember($cacheKey, Config::get('constants.cache.posts.ttlFile'), function() use(&$cacheKey,&$getQueryResultFromMainCacheOrDb){
            return $getQueryResultFromMainCacheOrDb($cacheKey);
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
        $userid = Auth::user()->id;
        $request->merge(['owner_id' => $userid]);
        $post = Post::create($request->only(['title', 'description', 'owner_id']));
        Event::dispatch(new PostCreated());
        $cacheKey = Config::get('constants.cache.posts.singleItemPrefix').$post->id;
        Artisan::call('cache:clear-expired');
        // use local file cache store to avoid repetitive load on main cache for non root page via cursor pagination
        return Cache::store('file')->remember($cacheKey, Config::get('constants.cache.posts.ttlFile'), function() use(&$cacheKey,&$post){
            return Cache::remember($cacheKey, Config::get('constants.cache.posts.ttlMain'), function() use(&$post){
                // load owner details weven though the owner is the only one who can create so that the newly created post is immidiately available as a cached response
                return new PostResource($post->load(['owner']));
            });
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

        $cacheKey = Config::get('constants.cache.posts.singleItemPrefix').$post->id;
        Artisan::call('cache:clear-expired');
        // use local file cache store to avoid repetitive load on main cache for non root page via cursor pagination
        return Cache::store('file')->remember($cacheKey, Config::get('constants.cache.posts.ttlFile'), function() use(&$cacheKey,&$post){
            return Cache::remember($cacheKey, Config::get('constants.cache.posts.ttlMain'), function() use(&$post){
                // load owner details weven though the owner is the only one who can create so that the newly created post is immidiately available as a cached response
                return new PostResource($post->load(['owner']));
            });
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
