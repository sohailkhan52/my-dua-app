@extends('layouts.app')

@section('content')
<div class="container-fluid">
<div class="col-md-6 mx-auto">
        <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Edit Translation</h3>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('translation.update', $translation->id) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="translator">Translator</label>
                    <input 
                        type="text" 
                        name="translator_name"
                        class="form-control"
                        value="{{ $translation->translator_name }}"
                        required>
                </div>

                <div class="mb-3">
                    <label for="category">Category</label>
                    <select name="category_id" class="form-control" required>

                        <option value="" disabled>Select Category</option>

                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ $translation->category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="mb-3">
                    <label for="language">Language</label>
                    <input 
                        type="text" 
                        name="language"
                        class="form-control"
                        value="{{ $translation->language }}"
                        required>
                </div>

                <button type="submit" class="btn btn-primary">
                    Update Translation
                </button>

            </form>

        </div>
    </div>
</div>
</div>
@endsection