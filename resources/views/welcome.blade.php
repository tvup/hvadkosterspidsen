@extends('layouts.app')

@section('title', $title)

@section('content')

    <div class="flex bg-white" style="height:600px;">
        <div class="flex items-center text-center lg:text-left  md:px-12 lg:w-1/2 grid">
            <div class="disclaimer-container">
                <div class="disclaimer-box p-4 m-4 bg-yellow-300 text-2xl rounded-md">
                    <p>
                        ca. DKK 16.000,-
                    </p>
                </div>
            </div>
        </div>
        <div class="hidden lg:block lg:w-1/2" style="clip-path:polygon(10% 0, 100% 0%, 100% 100%, 0 100%)">
            <img src="{{ asset('media/images/plane.png') }}" alt="Beskrivende tekst" class="h-full object-cover">
            <div class="h-full bg-black opacity-25 -z-10"></div>
        </div>

    </div>
@endsection
