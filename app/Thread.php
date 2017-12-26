<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;
	protected $guarded = [];
    protected static function boot(){
        parent::boot();

        static::addGlobalScope('replyCount',function($builder){
            return $builder->withCount('replies');
        });
        static::deleting(function($thread){
             $thread->replies->each->delete();
        });
    }
    
	public function path(){
		return "/threads/{$this->channel->slug}/{$this->id}";
	}
	//relationship with reply
    public function replies(){
    	return $this->hasMany(Reply::class)->withCount('favorites')->with('owner');
    }
    //relationship with channel
    public function channel(){
        return $this->belongsTo(Channel::class);
    }
    //relationship with user
    public function creator(){
    	return $this->belongsTo(User::class,'user_id');
    }
    //add a reply to the thread
    public function addReply($reply){
    	$this->replies()->create($reply);
    }
    public function scopeFilter($query,$filter){
        return $filter->apply($query);
    }

}
