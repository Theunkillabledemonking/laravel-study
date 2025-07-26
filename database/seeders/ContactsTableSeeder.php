<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $post = (Post::factory()->create([
            'title' => 'My greatest post ever',
        ]));

        // 복잡한 팩토리 형태의 팩토리
        User::factory()->count(20)->create()->each(function ($u) use ($post) {
            $post->comments()->save(Comment::factory()->make(
                [
                    'user_id' => $u->id,
                ]
                ));
        });
    }
}
