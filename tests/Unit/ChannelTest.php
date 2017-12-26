<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ChannelTest extends TestCase
{
	use DataBaseMigrations;

    /** @test */
    public function a_channel_consist_of_threads(){
    	$channel = create('App\Channel');
    	$thread = create('App\Thread',['channel_id'=> $channel->id]);
    	$this->assertTrue($channel->threads->contains($thread));
    }
}
