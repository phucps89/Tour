@extends('master')

@section('content')
    <iframe width="100%" frameborder="0" src="{{URL::to('/phpsysinfo/index.php')}}" style="
    height: 100vh;        /* Viewport-relative units */
    border: none;
    overflow: auto;
    ">

    </iframe>
@endsection