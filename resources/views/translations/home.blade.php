{{-- resources/views/translations/home.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        
        {{-- Card Header --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Translations</h3>
        </div>
        
        {{-- Card Body --}}
        <div class="card-body">
            
            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            {{-- Translations Table --}}
            <div class="col-md-10 mx-auto">
                
                {{-- Add Translation Button --}}
                <a href="translation/addtranslation" class="btn btn-primary mb-3">Add Translation</a>
                
                <table class='table bordered'>
                    <thead>
                        <tr>
                            <th>Translator</th>
                            <th>Category</th>
                            <th>Language</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($translations as $tran)
                        <tr>
                            {{-- Translator Name (links to show page) --}}
                            <td>
                                <a href="translation/show/{{ $tran->id }}">{{ $tran->translator_name }}</a>
                            </td>
                            
                            {{-- Category Name (links to show page) --}}
                            <td>
                                <a href="translation/show/{{ $tran->id }}">{{ $tran->category->name }}</a>
                            </td>
                            
                            {{-- Language Name (links to show page) --}}
                            <td>
                                <a href="translation/show/{{ $tran->id }}">{{ $tran->language->name }}</a>
                            </td>
                            
                            {{-- Action Buttons (Edit & Delete) --}}
                            <td class="d-flex">
                                {{-- Edit Button --}}
                                <a class="btn btn-sm btn-outline-primary" href="translation/edit/{{ $tran->id }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                {{-- Delete Button with Confirmation --}}
                                <a class="btn btn-sm btn-outline-danger" href="translation/delete/{{ $tran->id }}" onclick="return confirm('Are you sure you want to delete this translation?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
            </div> {{-- end col-md-5 --}}
            
        </div> {{-- end card-body --}}
        
    </div> {{-- end card --}}
</div> {{-- end container-fluid --}}
@endsection