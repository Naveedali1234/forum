<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;
    public function setUp(){
        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }
    /** @test */
    public function a_user_can_view_all_threads()
    {	
        $response = $this->get('/threads')
                        ->assertSee($this->thread->title);

        
    }
    /** @test */
    public function a_user_can_read_a_single_thread()
    {	
        $response = $this->get($this->thread->path())
                        ->assertSee($this->thread->title);

        //$response->assertSee($thread->title);
    }
    /** @test */
    public function a_user_can_read_replies_that_are_associated_with_thread(){
        //Given we have a thread
        // $thread come from setUp Method Above

        //thread include replies
        $reply = factory('App\Reply')->create(['thread_id'=> $this->thread->id]);

        //when we visit a thread page
        $response = $this->get($this->thread->path());
         //then we should see the replies
        $response->assertSee($reply->body);
    }
    /** @test */
    function a_user_can_filter_threads_according_to_a_channel(){
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread',['channel_id'=>$channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/'.$channel->slug)
        ->assertSee($threadInChannel->title)
        ->assertDontSee($threadNotInChannel->title);
           }
    /** @test */
    function a_user_can_filter_threads_by_any_username(){
            $this->signIn(create('App\User',['name'=>'JohnDoe']));

            $threadByJohn = create('App\Thread',['user_id'=> auth()->id()]);
            $threadNotByJohn = create('App\Thread');

            $this->get('threads?by=JohnDoe')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
           }
    /** @test */
    function a_user_can_filter_threads_by_popularity(){
        //given we have three threads
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply',['thread_id'=> $threadWithTwoReplies],2);

        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply',['thread_id'=> $threadWithThreeReplies],3);

        $threadWithNoReply = $this->thread;
        //when i filter all threads by popularity
        $response = $this->getJson('threads?popular=1')->json();
        //then they should be returned from most replies to least.
        $this->assertEquals([3,2,0],array_column($response, 'replies_count'));
    }
}
