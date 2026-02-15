@extends('layouts.asana')

@section('content')
<style>
    /* Forzar que el layout no imponga fondo blanco */
    main, .py-12 { background-color: transparent !important; }
</style>

<div class="py-8 md:py-12 px-4 md:px-8 max-w-[1600px] mx-auto">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('projects.index_content')
    </div>
</div>
@endsection