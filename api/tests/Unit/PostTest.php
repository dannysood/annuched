<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Str as Str;
use Illuminate\Support\Facades\Hash;

$faker = Faker::create();

class PostTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function postHasAOwner()
    {
        $faker = Faker::create();
        $user = User::create(['email' => $faker->unique()->safeEmail(), 'name' => $faker->name(), 'firebase_uid' => Str::random(28), 'password' => Hash::make(Str::random(30)), 'remember_token' => Str::random(10)]);
        $post = Post::create(['title' => $faker->sentence(6, true), 'description' => $faker->paragraphs(10, true), 'owner_id' => $user->id]);
        $this->assertEquals($post->owner->id, $user->id);
        $this->assertEquals($user->posts->first()->id, $post->id);
    }

    /** @test */
    public function postTestFillabe()
    {
        $faker = Faker::create();
        $user = User::create(['email' => $faker->unique()->safeEmail(), 'name' => $faker->name(), 'firebase_uid' => Str::random(28), 'password' => Hash::make(Str::random(30)), 'remember_token' => Str::random(10)]);
        $post = Post::create(['title' => $faker->sentence(6, true), 'description' => $faker->paragraphs(10, true), 'owner_id' => $user->id]);
        $this->assertEquals($post->getFillable(), ["title","description","owner_id"]);
    }
    /** @test */
    public function postTestHidden()
    {
        $faker = Faker::create();
        $user = User::create(['email' => $faker->unique()->safeEmail(), 'name' => $faker->name(), 'firebase_uid' => Str::random(28), 'password' => Hash::make(Str::random(30)), 'remember_token' => Str::random(10)]);
        $post = Post::create(['title' => $faker->sentence(6, true), 'description' => $faker->paragraphs(10, true), 'owner_id' => $user->id]);
        $this->assertEquals($post->getHidden(), ["updated_at","owner_id"]);
    }
}
