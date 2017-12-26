<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
class CreateThreadsTest extends TestCase
{
	use DatabaseMigrations;
	 /** @test */
	 function guest_may_not_create_threads(){
	     	//$thread = factory('App\Thread')->make();
	     	//or 2nd approach
	     	$thread = make('App\Thread');
        $this->get('threads/create')->assertRedirect('/login');
	     	$this->post('/threads')->assertRedirect('/login');
	 }
    /** @test */
    function an_authenticated_user_may_create_new_threads(){
    	//1-Given we have signed in user
    	//a- 1st approach
    	//$this->actingAs(factory('App\User')->create());
    	//b- 2nd approach using signIn methos in TestCase class as
    	$this->signIn();

    	//2-when we hit the endpoint to create new thread
    	//a- 1st approach
    	//$thread = factory('App\Thread')->make();
    	//b-2nd approach
    	$thread = make('App\Thread');
    	$response = $this->post("/threads",$thread->toArray());
    	//3-then we visit the thread page
    	$this->get($response->headers->get('Location'))
    	//4-we should see the new thread
    	->assertSee($thread->title)
    	->assertSee($thread->body);
    }

    /** @test */
    function a_thread_requires_a_title(){
        $this->publishThread(['title'=>null])
        ->assertSessionHasErrors('title');
    }
    /** @test */
    function a_thread_requires_a_body(){
        $this->publishThread(['body'=>null])
        ->assertSessionHasErrors('body');
    }
    /** @test */
    function a_thread_requires_a_valid_channel(){
       factory('App\Channel',2)->create();

       $this->publishThread(['channel_id'=>null])
       ->assertSessionHasErrors('channel_id');

       $this->publishThread(['channel_id'=>999])
       ->assertSessionHasErrors('channel_id');

    }
    /** @test */
    function unauthorized_users_may_not_delete_threads(){

        $thread = create('App\Thread');
        //dd($thread->path());
        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->json("DELETE",$thread->path())->assertStatus(403);
    }
    /** @test */
    function authorized_users_can_delete_threads(){
        $this->signIn();

        $thread = create('App\Thread',['user_id'=> auth()->id()]);
        $reply = create('App\Reply',['thread_id'=>$thread->id]);
        
        $response = $this->json('DELETE',$thread->path());
        
        $this->assertDatabaseMissing('threads', ['id' => $thread->id,'deleted_at'=>null]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id,'deleted_at'=>null]);
        $this->assertDatabaseMissing('activities', [
            'subject_id' => $thread->id,
            'subject_type'=> get_class($thread)
            
            ]);
        $this->assertDatabaseMissing('activities', [
            'subject_id' => $reply->id,
            'subject_type'=> get_class($reply)
            ]);

    }
    public function publishThread($overrides = []){
        $this->signIn();
        $thread = make('App\Thread',$overrides);
        return $this->post('/threads',$thread->toArray());

    }

}
