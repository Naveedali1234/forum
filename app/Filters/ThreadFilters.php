<?php

namespace App\Filters;
 class ThreadFilters extends Filters {

 	protected $filters=['by','popular'];
 	//filter the query by given username
	public function by($username){
		$user = \App\User::where('name',$username)->firstOrFail();

	        return $this->builder->where('user_id',$user->id);
	}
	//filter the query according to most popular threads
	protected function popular(){
		return $this->builder->orderBy('replies_count','desc');
	}
} 

 ?>