{{-- resources/views/fonts/index.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        
        {{-- Card Header --}}
        <div class="card-header mx-auto d-flex justify-content-between align-items-center">
            <h3>Fonts</h3>
        </div>
        
        {{-- Add Font Button --}}
        <div class="mx-auto">
            <a href="fonts/create" class="btn btn-outline-primary">Add Font</a>
        </div>
        
        {{-- Card Body --}}
        <div class="card-body">
            
            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Fonts Table --}}
            <div class="col-md-8 mx-auto">
                <table class="table bordered">
                    <thead>
                        <tr>
                            <th class="bg-dark text-light">Font Name</th>
                            <th class="bg-dark text-light">Download</th>
                            <th class="bg-dark text-light">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fonts as $font)
                        <tr>
                            {{-- Font Name --}}
                            <td>{{ $font->font_name }}</td>
                            
                            {{-- Download Button --}}
                            <td>
                                <a href="{{ route('fonts.download', $font) }}"
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </td>
                            
                            {{-- Edit & Delete Actions --}}
                            <td>
                                {{-- Edit Button --}}
                                <a href="{{ route('fonts.edit', $font) }}" class="mx-2 btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                {{-- Delete Form --}}
                                <form action="{{ route('fonts.destroy', $font) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this font?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        </div> {{-- end card-body --}}
        
    </div> {{-- end card --}}
</div> {{-- end container-fluid --}}
@endsection