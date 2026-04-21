@extends('layouts.app')

@section('content')
 @auth
    @livewire('chat')
@endauth
@endsection
