@extends('layouts.app')

@section('content')

<!-- Tabela Principal -->
<div class="main-content">
    <h2>Tabela Principal</h2>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Item 1</td>
                        <td>Descrição do Item 1</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Item 2</td>
                        <td>Descrição do Item 2</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Item 3</td>
                        <td>Descrição do Item 3</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
