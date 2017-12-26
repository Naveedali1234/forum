<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use RecordsActivity;
	protected $guarded = [];
    public function owner(){
    	return $this->belongsTo(User::class,'user_id');
    }
    public function favorites(){
    	return $this->morphMany(Favorite::class,'favorited');
    }
    public function favorite($userId){
    	$attribute = ['user_id'=>$userId];
    	if(! $this->favorites()->where($attribute)->exists()){
    		return $this->favorites()->create($attribute);
    	}
    }
    public function isFavorited(){
            return $this->favorites()->where('user_id',auth()->id())->exists();
        }
    public function thread(){
        return $this->belongsTo(Thread::class);
    }
}
