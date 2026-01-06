@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-3">Edit Produk</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('produks.update', $produk) }}" method="POST">
                @csrf @method('PUT')
                @include('produk._form', ['button' => 'Update'])
            </form>
        </div>
    </div>
</div>
@endsection