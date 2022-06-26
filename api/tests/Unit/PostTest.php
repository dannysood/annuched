<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str as Str;
use Illuminate\Support\Facades\Hash;



class PostTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test */
    public function postHasAOwner()
    {
        $user = User::create(['email' => $this->faker->unique()->safeEmail(), 'name' => $this->faker->name(), 'firebase_uid' => Str::random(28), 'password' => Hash::make(Str::random(30)), 'remember_token' => Str::random(10)]);
        $post = Post::create(['title' => $this->faker->sentence(6, true), 'description' => $this->faker->paragraphs(10, true), 'owner_id' => $user->id]);
        $this->assertEquals($post->owner->id, $user->id);
        $this->assertEquals($user->posts->first()->id, $post->id);
    }

    /** @test */
    public function postTestFillabe()
    {
        $user = User::create(['email' => $this->faker->unique()->safeEmail(), 'name' => $this->faker->name(), 'firebase_uid' => Str::random(28), 'password' => Hash::make(Str::random(30)), 'remember_token' => Str::random(10)]);
        $post = Post::create(['title' => $this->faker->sentence(6, true), 'description' => $this->faker->paragraphs(10, true), 'owner_id' => $user->id]);
        $this->assertEquals($post->getFillable(), ["title","description","owner_id"]);
    }
    /** @test */
    public function postTestHidden()
    {
        $user = User::create(['email' => $this->faker->unique()->safeEmail(), 'name' => $this->faker->name(), 'firebase_uid' => Str::random(28), 'password' => Hash::make(Str::random(30)), 'remember_token' => Str::random(10)]);
        $post = Post::create(['title' => $this->faker->sentence(6, true), 'description' => $this->faker->paragraphs(10, true), 'owner_id' => $user->id]);
        $this->assertEquals($post->getHidden(), ["updated_at","owner_id"]);
    }
}
