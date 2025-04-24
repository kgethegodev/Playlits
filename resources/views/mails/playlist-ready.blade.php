@extends('mails.layouts.mail')
@section('title', 'Playlist Ready')
@section('content')
    <p class="text-xl mb-4"><strong>Hi {{$user_name}},</strong></p>
    <p class="mb-2">Great news — your playlist has been successfully converted and is ready to enjoy!</p>
    <p class="mb-2">You can access it here: <a href="{{config("app.url") . '/playlists/' . $playlist->id }}" target="_blank" class="underline font-medium text-blue-500">{{$playlist->name}}</a> </p>
    <p class="mb-8">If you run into any issues or have feedback, we’d love to hear from you.</p>
@endsection
