<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    function unauthenticated_users_may_not_add_replies()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');

        $thread = factory('App\Thread')->create();

        $reply = factory('App\Reply')->create();
        $this->post($thread->path().'/replies', $reply->toArray());
    }
    /** @test */
     function an_authenticated_user_may_participate_in_forum_threads(){
    	//given we have authenticated user
    	$user = factory('App\User')->create();
        $this->be($user);
    	//And an existing thread
    	$thread = factory('App\Thread')->create();
    	
    	//when a user add a reply to the thread
    	$reply = factory('App\Reply')->make();
    	$this->post($thread->path().'/replies',$reply->toArray());
    	//then their rply should be visible on the page
    	$this->get($thread->path())
    		->assertSee($reply->body);
    }
    /** @test */
    function a_reply_requires_a_body(){
        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply',['body' => null]);

        $this->post($thread->path().'/replies',$reply->toArray())
        ->assertSessionHasErrors('body');

    }
}
