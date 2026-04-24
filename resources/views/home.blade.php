@extends('layouts.app')

@section('content')
        @role("admin")
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(auth()->user()->subscribed('default')) 
    print htmlspecialchars_decode
@endif
        <h3 class=mx-auto>Admin dashboard</h3>
        </div>
    </div>
</div>
        @endrole
        @role("user")
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

        <h3 class=mx-auto>user dashboard</h3>
        </div>
    </div>
</div>
        @endrole
@endsection
