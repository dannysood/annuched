<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str as Str;
use Illuminate\Support\Facades\Hash;



class PostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test */
    public function getPosts()
    {
        $firebaseUid = Str::random(28);
        $user = User::create(['email' => $this->faker->unique()->safeEmail(), 'name' => $this->faker->name(), 'firebase_uid' => $firebaseUid, 'password' => Hash::make(Str::random(30)), 'remember_token' => Str::random(10)]);
        Post::create(['title' => $this->faker->sentence(6, true), 'description' => $this->faker->paragraphs(10, true), 'owner_id' => $user->id]);

        Post::create(['title' => $this->faker->sentence(6, true), 'description' => $this->faker->paragraphs(10, true), 'owner_id' => $user->id]);
        $response = $this->json('GET', 'api/v1/post', [], [
            'firebase-uid-for-testing' => $firebaseUid
        ])->getData()->data;
        $postToTestFromRoute1 = $response[0];
        $postToTestFromRoute2 = $response[1];
        $posts = $user->posts()->get();
        $postToTestFromModel1 = $posts[1];
        $postToTestFromModel2 = $posts[0];
        $this->assertEquals($postToTestFromRoute1->id, $postToTestFromModel1->id);
        $this->assertEquals($postToTestFromRoute2->id, $postToTestFromModel2->id);
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
