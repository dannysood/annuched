<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str as Str;
use Illuminate\Support\Facades\Hash;



class PostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function getPostsWithPagination()
    {
        $firebaseUid = Str::random(28);
        $user = User::create(['email' => $this->faker->unique()->safeEmail(), 'name' => $this->faker->name(), 'firebase_uid' => $firebaseUid, 'password' => Hash::make(Str::random(30)), 'remember_token' => Str::random(10)]);
        $pageSize = Config::get('constants.pageSize');
        foreach(range(1,$pageSize*2) as $index) {
            sleep(2);

            Post::create(['title' => $this->faker->sentence(6, true), 'description' => $this->faker->paragraphs(10, true), 'owner_id' => $user->id]);
         }

        //  test first page
         $responseFirstPage = $this->json('GET', 'api/v1/post', [], [
            'firebase-uid-for-testing' => $firebaseUid
        ])->getData();
        $responseFirstPageData = $responseFirstPage->data;
        $responseNextPageCursor = $responseFirstPage->meta->next_cursor;
        $firstPostFromFirstPage = $responseFirstPageData[0];
        $posts = $user->posts()->get();
        $firstPostFromModel = $posts[0];
        $this->assertEquals($firstPostFromFirstPage->id, $firstPostFromModel->id);

        //  test next page
        $responseNextPage = $this->json('GET', 'api/v1/post', ['cursor' => $responseNextPageCursor], [
            'firebase-uid-for-testing' => $firebaseUid
        ])->getData();
        $responseNextPageData = $responseNextPage->data;
        $responsePrevPageCursor = $responseFirstPage->meta->prev_cursor;
        $firstPostFromNextPage = $responseNextPageData[0];
        $expectedFirstPostOnNextPage = $posts[0+$pageSize];
        $this->assertEquals($firstPostFromNextPage->id, $expectedFirstPostOnNextPage->id);

        //  test prev page
        $responsePrevPage = $this->json('GET', 'api/v1/post', ['cursor' => $responsePrevPageCursor], [
            'firebase-uid-for-testing' => $firebaseUid
        ])->getData();
        $responsePrevPageData = $responsePrevPage->data;
        $firstPostFromPrevPage = $responsePrevPageData[0];
        // since prev page from next page is first apge only
        $expectedFirstPostOnPrevPage = $posts[0];
        $this->assertEquals($firstPostFromPrevPage->id, $expectedFirstPostOnPrevPage->id);

    }

    /** @test */
    public function getOnePost()
    {
        $firebaseUid = Str::random(28);
        $user = User::create(['email' => $this->faker->unique()->safeEmail(), 'name' => $this->faker->name(), 'firebase_uid' => $firebaseUid, 'password' => Hash::make(Str::random(30)), 'remember_token' => Str::random(10)]);
        $post = Post::create(['title' => $this->faker->sentence(6, true), 'description' => $this->faker->paragraphs(10, true), 'owner_id' => $user->id]);
        $url = 'api/v1/post/'.$post->id;
        $postToTest = $this->json('GET', $url, [], [
            'firebase-uid-for-testing' => $firebaseUid
        ])->getData()->data;
        $this->assertEquals($postToTest->id, $post->id);
        $this->assertEquals($postToTest->owner_id, $post->owner_id);
    }

    /** @test */
    public function createNewPost()
    {
        $firebaseUid = Str::random(28);
        $user = User::create(['email' => $this->faker->unique()->safeEmail(), 'name' => $this->faker->name(), 'firebase_uid' => $firebaseUid, 'password' => Hash::make(Str::random(30)), 'remember_token' => Str::random(10)]);
        $title = $this->faker->sentence(6, true);
        $description = $this->faker->paragraphs(10, true);
        $response = $this->json('POST', 'api/v1/post', [
            'title' => $title,
            'description' => $description
        ], [
            'firebase-uid-for-testing' => $firebaseUid
        ])->getData()->data;
        $post = $user->posts->first();
        $this->assertEquals($post->id, $response->id);
        $this->assertEquals($post->title, $title);
        $this->assertEquals($post->description, $description);
        $this->assertEquals($post->owner_id, $user->id);
    }
}
