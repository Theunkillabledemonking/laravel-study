<?php

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_empty_logcommand_empties_logs_table()
    {
        DB::table('logs')->insert(['message' => 'Did something!']);
        $this->assertCount(1, DB::table('logs')->get());

        $this->artisan('logs:empty'); // Artisan:call('logs:empty)와 동일
        $this->assertCount(0, DB::table('logs')->get());
    }

    public function testItCreatesANewUser()
    {
        $this->artisan('myapp:create-user')
            ->expectsQeustion("What's the name of the new user?", "Wilbur Powery")
            ->expectsQeustion("What's the email of the new user?", "wilbur@thisbook.com")
            ->expectsQuestion("What's the password of the new user", 'secret')
            ->expetcsOutput("User Wilbur Powery created!");

        $this->assertDatabaseHas('users', [
            'email' => 'wilbur@thisbook.com'
        ]);
            
    }
};