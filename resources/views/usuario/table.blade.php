@if(count($usuarios) > 0)
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th scope="col">Nome</th>
            <th scope="col">Email</th>
            <th scope="col">Perfil</th>
            <th scope="col">Verificação</th>
            <th scope="col">Status</th>
            <th scope="col">Criado em</th>
        </tr>
    </thead>
    <tbody>
        @foreach($usuarios as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->admin ? 'Administrador' : 'Usuário' }}</td>
                <td>{{ $item->email_verified_at ? \App\Util::formatarDataHora($item->email_verified_at) : 'Pendente' }}</td>
                <td>
                    @if($item->deleted_at)
                        <span class="badge text-bg-danger p-2">Inativo</span>
                    @else
                        <span class="badge text-bg-success p-2">Ativo</span>
                    @endif
                </td>
                <td>{{ \App\Util::formatarDataHora($item->created_at) }}</td>
                <td>
                    <button class="btn btn-warning" onclick="modalEditar({{ $item }})"><i class="bi bi-pencil-fill"></i></button>
                    @unless($item->admin || $item->deleted_at)
                        <button class="btn btn-danger" onclick="modalExcluir({{ $item->id }}, '{{ $item->name }}')"><i class="bi bi-trash3-fill"></i> </button>
                    @endunless

                    @if($item->deleted_at)
                        <button class="btn btn-success" onclick="modalAtivar({{ $item->id }}, '{{ $item->name }}')"><i class="bi bi-arrow-clockwise"></i> </button>
                    @endif
                </td>

            </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- Menu de paginação -->
@if($usuarios->lastPage() > 1)
    @include('layouts/pagination', ['data' => $usuarios])
@endif
