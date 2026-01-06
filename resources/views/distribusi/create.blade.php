@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">
    <h4 class="fw-bold mb-3">Buat Distribusi</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('distribusis.store') }}" method="POST">
                @csrf
                @include('distribusi._form', ['button' => 'Simpan'])
            </form>
        </div>
    </div>
</div>
@endsection