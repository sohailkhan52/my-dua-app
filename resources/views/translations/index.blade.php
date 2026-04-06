{{-- resources/views/translations/index.blade.php --}}

@extends('layouts.app')

@section('content')

<div class="container-fluid">
    
    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="card">
        
        {{-- Card Header with Title and Font Selector --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Translations</h3>

            {{-- Font Dropdown Selector --}}
            <div class="d-flex align-items-center">
                <ul id="simple-olor" class="form-control" style="width: 200px;">
                    <p>Fonts</p>
                    @foreach($fonts as $font)
                        <li data-path="{{ asset('storage/'.$font->font_path) }}">
                            <a href="/fontt/{{ $font->id }}">
                                {{ ucfirst(strToLower($font->font_name))}}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        
        <div class="card-body">

            {{-- Surah Download Form --}}
            <div class="mb-3">
                <form action="/surah" method="post">
                    @csrf
                    
                    {{-- Surah Selection Dropdown --}}
                    <select name="surah_no" id="">
                        <option>Select Surah</option>
                        @foreach($surahs as $surah)
                            <option value="{{ $surah }}">Surah No {{ $surah }}</option>
                        @endforeach
                    </select>
                    
                    {{-- Hidden Translation ID --}}
                    <input type="hidden" name="translationId" value="{{ $mainTranslation->id }}">
                    
                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-sm btn-primary">Download</button>
                </form>
            </div>

            {{-- Get selected font file path --}}
            @php
                $selectedFont = $defaultFont;
                $fontFile = asset('storage/' . $fonts->where('id', $selectedFont)->first()->font_path);
            @endphp

            {{-- Custom Font Styles --}}
            <style>
                @font-face {
                    font-family: 'CustomFont';
                    src: url('{{ $fontFile }}');
                }

                .arabic-text {
                    font-family: 'CustomFont';  /* Font for Arabic text */
                }
                
                .translation-text {
                    font-family: 'Noto Nastaliq Urdu';  /* Font for translation text */
                }
            </style>

            {{-- Translation Table Container --}}
            <div id="translationContainer">
                <table class='table bordered'>
                    <thead>
                        <tr>
                            <th>Translation Language</th>
                            <th>Surah No</th>
                            <th>Ayah No</th>
                            @if($flag === 1)
                                <th>Word No</th>  {{-- Extra column for word level --}}
                            @endif
                            <th>Text</th>
                            <th>Arabic Text</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        {{-- VERSE LEVEL TRANSLATION (flag != 1) --}}
                        @if($flag !== 1)
                            @foreach($translation as $tran)
                                @php
                                    // Find matching Arabic text for this verse
                                    $arabic_word = $arabic
                                        ->where('ayah_number', $tran->ayah_number)
                                        ->where('surah_number', $tran->surah_number)
                                        ->first();
                                @endphp
                                <tr>
                                    <td class="translation-text">{{ $tran->translation->language->name }}</td>
                                    <td>{{ $tran->surah_number }}</td>
                                    <td>{{ $tran->ayah_number }}</td>
                                    <td class="translation-text">{!! $tran->verse_text !!}</td>
                                    <td class="arabic-text">{{ $arabic_word->arabic_text ?? '' }}</td>
                                </tr>
                            @endforeach
                        
                        {{-- WORD LEVEL TRANSLATION (flag == 1) --}}
                        @else
                            @foreach($translation as $tran)
                                @php
                                    // Find matching Arabic text for this word (by surah, ayah, AND word number)
                                    $arabic_word = $arabic
                                        ->where('ayah_number', $tran->ayah_number)
                                        ->where('surah_number', $tran->surah_number)
                                        ->where('word_number', $tran->word_number)
                                        ->first();
                                @endphp
                                <tr>
                                    <td class="translation-text">{{ $tran->translation->language->name }}</td>
                                    <td>{{ $tran->surah_number }}</td>
                                    <td>{{ $tran->ayah_number }}</td>
                                    <td>{{ $tran->word_number }}</td>
                                    <td class="translation-text">{!! $tran->word_text !!}</td>
                                    <td class="arabic-text">{{ $arabic_word->arabic_text ?? '' }}</td>
                                </tr>
                            @endforeach
                        @endif
                        
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination Links --}}
            {!! $translation->onEachSide(1)->links('pagination::simple-bootstrap-4') !!}
            
        </div> {{-- end card-body --}}
        
    </div> {{-- end card --}}
    
</div> {{-- end container-fluid --}}

@endsection