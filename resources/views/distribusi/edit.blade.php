@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">
    <h4 class="fw-bold mb-3">Edit Distribusi</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('distribusis.update', $distribusi) }}" method="POST">
                @csrf @method('PUT')
                @include('distribusi._form', ['button' => 'Update'])
            </form>
        </div>
    </div>
</div>
@endsection