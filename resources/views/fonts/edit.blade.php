{{-- resources/views/fonts/edit.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Center the form on the page using 4-column width on medium screens --}}
    <div class="col-md-4 mx-auto">

        <div class="card">

            {{-- Card header with title --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Update Font</h3>
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

                {{-- Form to update existing font --}}
                <form action="{{ route('fonts.update', $font->id) }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')  {{-- Using PUT method for update --}}

                    {{-- Font name input field (pre-filled with existing value) --}}
                    <div class="mb-3">
                        <label>Font Name</label>
                        <input type="text" name="font_name" class="form-control" value="{{ $font->font_name }}" required>
                    </div>

                    {{-- Font file upload field (optional for update) --}}
                    <div class="mb-3">
                        <label>Font File</label>
                        <input type="file" name="font_file" class="form-control">
                        <small class="text-muted">Leave empty to keep existing font file</small>
                    </div>

                    {{-- Submit button --}}
                    <button type="submit" class="btn btn-primary">
                        Update Font
                    </button>

                </form>

            </div> {{-- end card-body --}}

        </div> {{-- end card --}}

    </div> {{-- end col-md-4 --}}

</div> {{-- end container-fluid --}}
@endsection