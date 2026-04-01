@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header mx-auto d-flex justify-content-between align-items-center">
            <h3>Fonts</h3>            
        </div>
            <div class=" mx-auto">
            <a href="fonts/create" class="btn btn-outline-primary">Add Font</a>

          </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

           <div class="col-md-6 mx-auto">
             <table class="table bordered">
                <thead>
                    <th class="bg-dark text-light">Font Name</th>
                    <th class="bg-dark text-light">Download</th>
                    <th class="bg-dark text-light">Actions</th>
                </thead>
                <tbody>
                    @foreach($fonts as $font)
                    <tr>
                        <td>{!! $font->font_name!!}</td>
                        <td>
                                <a href="{{ route('fonts.download', $font) }}" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </td>
                        <td><a href="{{ route('fonts.edit', $font) }}" class="mx-2 btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{!!route('fonts.destroy',$font)!!}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this font?');">
                            @csrf
                            @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> 
                                    </button>
                        </form></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
           </div>
        </div>
    </div>
</div>
@endsection