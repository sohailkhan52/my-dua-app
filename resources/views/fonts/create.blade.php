{{-- resources/views/fonts/create.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Center the form on the page using 4-column width on medium screens --}}
    <div class="col-md-4 mx-auto">

        <div class="card">

            {{-- Card header with title --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>New Font</h3>
            </div>

            <div class="card-body">

                {{-- Display success message if any --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- Display validation errors if any --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form to create new font --}}
                <form action="{!! route('fonts.store') !!}" method="POST" enctype="multipart/form-data">

                    @csrf
                    @method('POST')

                    {{-- Font name input field --}}
                    <div class="mb-3">
                        <label>Font Name</label>
                        <input type="text" name="font_name" class="form-control" required>
                    </div>

                    {{-- Font file upload field --}}
                    <div class="mb-3">
                        <label>Font File</label>
                        <input type="file" name="font_file" class="form-control" required>
                    </div>

                    {{-- Submit button --}}
                    <button type="submit" class="btn btn-primary">
                        Create Font
                    </button>

                </form>

            </div> {{-- end card-body --}}

        </div> {{-- end card --}}

    </div> {{-- end col-md-4 --}}

</div> {{-- end container-fluid --}}
@endsection