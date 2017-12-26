@component('profiles.activities.activity')
	@slot('header')
		{{$user->name}} has replied to
		<a href="{{$activity->subject->thread->path()}}">{{$activity->subject->thread->title}}</a>
	@endslot
	@slot('body')
		{{$activity->subject->body}}
	@endslot
@endcomponent