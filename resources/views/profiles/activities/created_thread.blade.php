@component('profiles.activities.activity')
	@slot('header')
		{{$user->name}} has posted
		<a href="{{$activity->subject->path()}}">{{$activity->subject->title}}</a>
	@endslot
	@slot('body')
		{{$activity->subject->body}}
	@endslot
@endcomponent

