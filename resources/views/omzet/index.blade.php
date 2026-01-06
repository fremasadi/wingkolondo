@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Omzet Penjualan</h4>
    </div>

    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <input type="date" name="mulai"
                   value="{{ $mulai }}"
                   class="form-control">
        </div>
        <div class="col-md-3">
            <input type="date" name="sampai"
                   value="{{ $sampai }}"
                   class="form-control">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary">
                <i class="bx bx-filter"></i> Filter
            </button>
        </div>
    </form>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6>Cash & Transfer</h6>
                    <h4 class="text-success">
                        Rp {{ number_format($cash) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6>Tempo (Lunas)</h6>
                    <h4 class="text-info">
                        Rp {{ number_format($tempo) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border border-primary">
                <div class="card-body">
                    <h6>Total Omzet</h6>
                    <h3 class="fw-bold text-primary">
                        Rp {{ number_format($totalOmzet) }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection