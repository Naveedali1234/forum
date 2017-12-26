<?php

namespace App\Http\Controllers;
use App\Filters\ThreadFilters;
use App\Thread;
use App\Reply;
use Illuminate\Http\Request;
use App\Channel;
class ThreadsController extends Controller
{
    public function __construct(){
        $this->middleware('auth')->except(['show','index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilters $filter)
    {

        $threads = $this->getThreads($channel,$filter);
        if(request()->wantsJson()){
            return $threads;
        }

        //if request('by'), we should filter by given username
        // if($username = request('by')){
        //     $user = \App\User::where('name',$username)->firstOrFail();

        //     $threads->where('user_id',$user->id);
        // }
        // $threads = $threads->get();
        return view('threads.index',compact('threads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,
            [
            'title' => 'required',
            'body' => 'required',
            'channel_id'=>'required|exists:channels,id'
            ]);
        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => $request->channel_id,
            'title' => $request->title,
            'body' => $request->body
            ]);
        return redirect($thread->path())->with('flash','Thread has been created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread)
    {
        return view('threads.show',[
            'thread'=>$thread,
            'replies'=>$thread->replies()->paginate(1)
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel,Thread $thread)
    {
        $this->authorize('update',$thread);
        $thread->delete();
        if(request()->wantsJson()){
           return $this->response([],'204'); 
        }
        return redirect('/threads');
       
    }
    protected function getThreads(Channel $channel, ThreadFilters $filters){
        $threads = Thread::with('channel')->filter($filters);
        if($channel->exists)
        {
            $threads->where('channel_id',$channel->id);
        }
        return $threads->get();
    }
}
