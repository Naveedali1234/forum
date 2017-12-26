<?php

namespace App\Http\Controllers;
use App\Favorite;
use Illuminate\Http\Request;
use App\Reply;
class FavoritesController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}
    public function store(Reply $reply){
    	 $reply->favorite(auth()->id());
    	 return back();
    }
}
