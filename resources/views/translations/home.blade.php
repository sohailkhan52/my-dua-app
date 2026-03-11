@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Translations</h3>

        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- <div class="col-md-3 mx-auto">
              <form method="post" action="/cat">
                  <select name="category_id" class="form-control">
                      <option value="" disabled>Select Category</option>

                      @foreach($categories as $cat)
                          <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                      @endforeach

                  </select>

                  <button type="submit">Go</button>
              </form>
            </div> -->


            <div class="col-md-5 mx-auto">
                <a href="translation/addtranslation" class="btn btn-primary">Add Translation</a>
                <table class='table bordered'>
                     <thead>
                         <th>Translator</th>
                         <th>category</th>
                         <th>Language</th>
                         <th>Downloading Link</th>
                         <th>Actions</th>
                     </thead>
                     <tbody>
                 @foreach($translations as $tran)
                       <tr>
                        <td><a href="translation/show/{{$tran->id}}">{{$tran->translator_name}}</a></td>
                        <td><a href="translation/show/{{$tran->id}}">{{$tran->category->name}}</a></td>
                        <td><a href="translation/show/{{$tran->id}}">{{$tran->language->name}}</a></td>
                        <td><a href="translation/show/{{$tran->id}}">{{$tran->language->name}}</a></td>
                        <td class="d-flex "><a class="btn btn-sm btn-outline-primary" href="translation/edit/{{$tran->id}}"><i class="bi bi-pencil"></i></a>
                          <a class="btn btn-sm btn-outline-danger" href="translation/delete/{{$tran->id}}"onclick="return confirm('Are you sure you want to delete this translation?');"><i class="bi bi-trash"></i></a></td>
                         </tr> 
                 @endforeach
                     </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection