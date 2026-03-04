@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

        @role("admin")
        <h3 class=mx-auto>Admin dashboard</h3>
        @endrole

        @role("user")
        <h3 class=mx-auto>User dashboard</h3>
        @endrole

        </div>
    </div>
</div>
@endsection
