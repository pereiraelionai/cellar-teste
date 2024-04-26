@if(count($categorias) > 0)
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Nome</th>
            <th scope="col">Criado por</th>
            <th scope="col">Criado em</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categorias as $item)
            <tr>
                <th scope="row">{{ $item->id }}</th>
                <td>{{ $item->nome }}</td>
                <td>{{ $item->nome_usuario }}</td>
                <td>{{ \App\Util::formatarDataHora($item->created_at) }}</td>
                <td>
                    <button class="btn btn-warning"><i class="bi bi-pencil-fill"></i> </button>
                    <button class="btn btn-danger" onclick="modalExcluir({{ $item->id }}, '{{ $item->nome }}')"><i class="bi bi-trash3-fill"></i> </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="container my-2">
    <div class="p-4 text-center bg-body-tertiary rounded-3">
        <p class="lead">
            Nenhuma categoria registrada.
        </p>
    </div>
</div>
@endif

<!-- Menu de paginação -->
@if($categorias->lastPage() > 1)
    @include('layouts/pagination', ['data' => $categorias])
@endif