@extends('layouts.app')

@section('content')
<h2>{{ $book->title }}</h2>
<p>Автор: {{ $book->author }}</p>
<p>{{ $book->description }}</p>
<strong>{{ $book->price }} BYN</strong>
<button onclick="addToCart({{ $book->id }})">В корзину</button>
@endsection
