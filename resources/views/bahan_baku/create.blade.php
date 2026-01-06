@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-3">Tambah Bahan Baku</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('bahan-bakus.store') }}" method="POST">
                @csrf
                @include('bahan_baku._form', ['button' => 'Simpan'])
            </form>
        </div>
    </div>
</div>
@endsection