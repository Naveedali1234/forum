<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function an_authenticated_user_can_favorite_any_reply(){
        $this->signIn();
        $reply = create('App\Reply');
        $this->post('/replies/'.$reply->id.'/favorites');
        $this->assertCount(1,$reply->favorites);
    }
    /** @test */
    function an_authenticated_user_may_only_favorite_a_reply_once(){
        $this->signIn();
        $reply = create('App\Reply');
        try {
            $this->post('/replies/'.$reply->id.'/favorites');
            $this->post('/replies/'.$reply->id.'/favorites');
            
        } catch (Exception $e) {
            $this->fail('did not expect to insert same record');
        }
        $this->assertCount(1,$reply->favorites);
    }
   
}
