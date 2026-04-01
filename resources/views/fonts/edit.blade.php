@extends('layouts.app')

@section('content')
<div class="container-fluid">

<div class="col-md-4 mx-auto">



        <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Update Font</h3>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
       @endif

            <form action="{{ route('fonts.update',$font->id) }}" method="POST" enctype="multipart/form-data">

                @csrf
                @method('Put')

                <div class="mb-3">
                    <label>Font</label>
                    <input type="text" name="font_name" class="form-control" value="{!! $font->font_name !!}" required>
                </div>



                </div>
                <div class="mb-3">
                    <label >File Name</label>
                    <input type="file"name="font_file" class="form-control"required value="{!! $font->font_file !!}">
                </div>

                <button type="submit" class="btn btn-primary">
                    Update Font
                </button>

            </form>

  
</div>
</div>
</div>
@endsection