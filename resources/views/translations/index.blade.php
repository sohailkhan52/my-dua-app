@extends('layouts.app')

@section('content')

<div class="container-fluid">
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Translations</h3>

            <div class="d-flex align-items-center">
                <ul id="simple-olor" class="form-control" style="width: 200px;">
                    <p>Fonts</p>
                    @foreach($fonts as $font)
                        <li  data-path="{{ asset('storage/'.$font->font_path) }}">
                            <a href="/fontt/{{$font->id }}">
                            {{ $font->font_name }}
                        </a></li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="card-body">



<div class="mb-3"> 

<form action="/surah" method=post>
    @csrf 
    <select name="surah no" id="">
        <option > Select Surah</option>
        @foreach($surahs as $surah)
        <option value="{{$surah}}">Surah No {{$surah}}</option>
        @endforeach
    </select>    
        <input type="hidden" name="translationId" value="{{$mainTranslation->id}}">
<button type=submit class="btn btn-sm btn-primary">Download</button>
</form>

</div>

@php
    $selectedFont =  $defaultFont;
    $fontFile =  asset('storage/' . $fonts->where('id', $selectedFont)->first()->font_path);
@endphp

<style>
    @font-face {
        font-family: 'CustomFont';
        src: url('{{ $fontFile }}');
    }
    
    .arabic-text {
        font-family: 'CustomFont';
    }
    .translation-text {
        font-family: 'Noto Nastaliq Urdu';
    }
</style>


<div  id="translationContainer">
    <table class='table bordered'>
    <thead>
        <th>Translation Language</th>
        <th>Surah No</th>
        <th>Ayah No</th>
        @if($flag===1)
        <th>Ayah No</th>
        @endif
        <th >Text</th>
        <th>Arabic Text</th>
    </thead>
    <tbody>
       @if($flag!==1)
   @foreach($translation as $tran)
        @php
        $arabic_word=$arabic
        ->where('ayah_number',$tran->ayah_number)
        ->where('surah_number',$tran->surah_number)->first();
        @endphp
      <tr>
       <td class="translation-text">{!! $tran->translation->language->name!!}</td>
       <td>{!! $tran->surah_number !!}</td>
       <td>{!! $tran->ayah_number !!}</td>
       <td class="translation-text">{!! $tran->verse_text!!}</td>
       <td class="arabic-text">{!! $arabic_word->arabic_text!!}</td>
        </tr> 
   @endforeach
       @else
        @foreach($translation as $tran)
        @php
        $arabic_word=$arabic
        ->where('ayah_number',$tran->ayah_number)
        ->where('surah_number',$tran->surah_number)
        ->where('word_number',$tran->word_number)->first();
        @endphp
      <tr>
       <td class="translation-text">{!! $tran->translation->language->name !!}</td>
       <td>{!! $tran->surah_number !!}</td>
       <td>{!! $tran->ayah_number !!}</td>
       <td>{!! $tran->word_number !!}</td>
       <td class="translation-text">{!! $tran->word_text !!}</td>
       <td class="arabic-text">{!! $arabic_word->arabic_text !!}</td>
        </tr> 
   @endforeach

   @endif
    </tbody>
   </table>
</div>
{!! $translation->onEachSide(1)->links('pagination::simple-bootstrap-4') !!}
        </div>
    </div>
</div>

@endsection

