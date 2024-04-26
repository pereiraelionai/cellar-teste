@if(count($categorias) > 0)
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th scope="col">Nome</th>
            <th scope="col">Criado por</th>
            <th scope="col">Criado em</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categorias as $item)
            <tr>
                <td>{{ $item->nome }}</td>
                <td>{{ $item->usuarios->name }}</td>
                <td>{{ \App\Util::formatarDataHora($item->created_at) }}</td>
                <td>
                @if(session('permissao')->criar_editar)
                    <button class="btn btn-warning" onclick="modalEditar({{ $item->id }}, '{{ $item->nome }}')"><i class="bi bi-pencil-fill"></i> </button>
                @endif
                @if(session('permissao')->excluir)
                    <button class="btn btn-danger" onclick="modalExcluir({{ $item->id }}, '{{ $item->nome }}')"><i class="bi bi-trash3-fill"></i> </button>
                @endif
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