@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-3">Tambah Toko</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tokos.store') }}" method="POST">
                @csrf
                @include('toko._form', ['button' => 'Simpan'])
            </form>
        </div>
    </div>
</div>
@endsection