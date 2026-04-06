{{-- resources/views/translations/create.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Center the form on the page using 6-column width on medium screens --}}
    <div class="col-md-6 mx-auto">
                {{-- Success Message --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
        {{-- CREATE MODE (flag == 1) --}}
        @if($flag == 1)
        <div class="card">

            {{-- Card Header --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>New Translation</h3>
            </div>

            <div class="card-body">

                {{-- Success Message --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Create Translation Form --}}
                <form action="{{ route('translation.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    @method('POST')

                    {{-- Translator Name --}}
                    <div class="mb-3">
                        <label>Translator</label>
                        <input type="text" name="translator_name" class="form-control" required>
                    </div>

                    {{-- Category Dropdown --}}
                    <div class="mb-3">
                        <label>Category</label>
                        <select name="category_id" class="form-control" required>
                            <option value="" disabled>Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Language Dropdown --}}
                    <div class="mb-3">
                        <label>Language</label>
                        <select name="language_id" class="form-control" required>
                            <option value="" disabled>Select Language</option>
                            @foreach($languages as $lan)
                                <option value="{{ $lan->id }}">
                                    {{ $lan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- JSON File Upload --}}
                    <div class="mb-3">
                        <label>File Name</label>
                        <input type="file" name="file_name" class="form-control" required>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-primary">
                        Create Translation
                    </button>

                </form>

            </div> {{-- end card-body --}}
        </div> {{-- end card --}}

        {{-- EDIT MODE (flag == 0) --}}
        @else
        <div class="card">

            {{-- Card Header --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Edit Translation</h3>
            </div>

            <div class="card-body">

                {{-- Success Message --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- Edit Translation Form --}}
                <form action="{{ route('translation.update', $translation->id) }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    {{-- Translator Name (pre-filled) --}}
                    <div class="mb-3">
                        <label for="translator">Translator</label>
                        <input type="text" name="translator_name" class="form-control" value="{{ $translation->translator_name }}" required>
                    </div>

                    {{-- Category Dropdown (pre-selected) --}}
                    <div class="mb-3">
                        <label for="category">Category</label>
                        <select name="category_id" class="form-control" required>
                            <option value="" disabled>Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $translation->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Language Dropdown --}}
                    <div class="mb-3">
                        <label for="language">Language</label>
                        <select name="language_id" class="form-control" required>
                            <option value="" disabled>Select Language</option>
                            @foreach($languages as $lan)
                                <option value="{{ $lan->id }}">
                                    {{ $lan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- JSON File Upload --}}
                    <div class="mb-3">
                        <label>File Name</label>
                        <input type="file" name="file_name"  class="form-control" >
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-primary">
                        Update Translation
                    </button>

                </form>

            </div> {{-- end card-body --}}
        </div> {{-- end card --}}
        @endif

    </div> {{-- end col-md-6 --}}

</div> {{-- end container-fluid --}}
@endsection
