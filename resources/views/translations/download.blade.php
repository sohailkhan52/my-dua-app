@extends('layouts.app')

@section('content')
<style>
@font-face {
    font-family: 'urdu';
    src: url("file:///{{ str_replace('\\', '/', storage_path('fonts/NotoNastaliqUrdu-Regular.ttf')) }}") format("truetype");
}

body {
    font-family: 'urdu';
}

.rtl {
    direction: rtl;
    text-align: right;
}
</style>
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Translations</h3>
        </div>
        <div class="card-body">
<table class='table bordered'>
    <thead>
        <th>Translation Language</th>
        <th>Surah No</th>
        <th>Ayah No</th>
        @if($flag===1)
        <th>Ayah No</th>
        @endif
        <th>Translation</th>
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
       <td>{!! $tran->translation->language->name!!}</td>
       <td>{!! $tran->surah_number !!}</td>
       <td>{!! $tran->ayah_number !!}</td>
       <td class="rtl">{!! $tran->verse_text!!}</td>
       <td class="quran-uthmanic rtl">{!! $arabic_word->arabic_text!!}</td>
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
       <td>{!! $tran->translation->language->name !!}</td>
       <td>{!! $tran->surah_number !!}</td>
       <td>{!! $tran->ayah_number !!}</td>
       <td>{!! $tran->word_number !!}</td>
       <td class="rtl">{!! $tran->word_text !!}</td>
       <td class="rtl">{!! $arabic_word->arabic_text !!}</td>
        </tr> 
@endforeach

@endif
    </tbody>
</table>
        </div>
    </div>
</div>

@endsection