@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-3">Edit Toko</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tokos.update', $toko) }}" method="POST">
                @csrf @method('PUT')
                @include('toko._form', ['button' => 'Update'])
            </form>
        </div>
    </div>
</div>
@endsection