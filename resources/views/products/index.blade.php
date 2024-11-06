@extends('layouts.app')

@section('content')
    <div id="app">
        <product-list
            :initial-products='@json($products)'
            :categories='@json($categories)'
            :filters='@json(request()->only(["category_id", "sort_price"]))'
        />
    </div>
@endsection
