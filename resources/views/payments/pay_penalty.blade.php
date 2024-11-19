@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Book Title : {{ $borrowing->book->title }}</h3>
        <br>
        <h3>Book Id : {{ $borrowing->id }}</h3>
        <br>
        <h3>Penalty Total Amount : ₹{{ $penalty }}</h3>
        <br>
        <form action="{{ route('process.payment', $borrowing->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Proceed to Payment</button>
        </form>
    </div>
@endsection
