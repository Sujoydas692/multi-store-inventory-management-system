@extends('layouts.app')

@section('content')

<style>
    html, body {
        height: 100%;
        margin: -30px 0px 0px 0px;
        overflow: hidden; /* 🔑 prevents scrolling */
    }
</style>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100">
            <div class="col-12 mt-5">
                <div class="bg-light p-5">
                    <h1 class="text-center">S H O P N O</h1>
                    <h6 class="text-center">MULTI STORE INVENTORY MANAGEMENT SYSTEM</h6>
                    <hr>
                    <div class="text-center">
                        <a class="btn bg-gradient-primary" href="{{ route('login') }}">Start Sell</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
