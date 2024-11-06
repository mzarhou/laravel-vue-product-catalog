@extends('layouts.app')

@section('content')
    <div id="app">
        <product-form
            :categories='@json($categories)'
            :csrf-token="'{{ csrf_token() }}'"
            submit-url="{{ route('products.store') }}"
        />
    </div>
@endsection
