@extends('layouts.app')

@section('content')

<div class="main-content">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
        <div class="row mb-2">
            <div class="col div-titulo" id="titulo-head">
                <h1 class="h2">Produtos</h1>
            </div>
        </div>
        <div class="btn-toolbar mb-2">
            <button class="btn btn-success">Novo Produto</button>                       
        </div>
    </div>

    <div class="card">
        <div class="card-body" id="table-categorias">
        @include('produto/table')
        </div>
    </div>

</div>

@endsection

@section('js')

<script>

</script>

@endsection