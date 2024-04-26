@extends('layouts.app')

@section('content')

<div class="main-content">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
        <div class="row mb-2">
            <div class="col div-titulo" id="titulo-head">
                <h1 class="h2">Categorias</h1>
            </div>
        </div>
        <div class="btn-toolbar mb-2">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#novaCategoriaModal" onclick="limparModal()">Nova Categoria</button>                       
        </div>
    </div>

    <div class="card">
        <div class="card-body" id="table-categorias">
            @include('categoria/table')
        </div>
    </div>

</div>

<!-- Modal cadastrar caegoria -->
<div class="modal fade" id="novaCategoriaModal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Nova Categoria</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <label for="nomeCategoria">Nome</label>
            <input type="text" id="nomeCategoria" class="form-control">
            <div class="invalid-feedback"></div>
            <input type="hidden" id="token" value="{{ csrf_token() }}">
       </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-success" onclick="salvar()">Salvar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal excluir categoria -->
<div class="modal fade" id="excluirCategoriaModal" aria-hidden="true" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Excluir Categoria</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <p>Tem certeza que deseja excluir a categoria <strong id="nomeCategoriaExcluir"></strong>?</p>
            <input type="hidden" id="token" value="{{ csrf_token() }}">
            <input type="hidden" id="id_categoria">
       </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-danger" onclick="excluir()">Excluir</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')

<script>

    function salvar()
    {
        var data = {
            nome: document.getElementById('nomeCategoria').value,
            _token: document.getElementById('token').value
        };

        $.ajax({
            type: 'POST',
            url: '/categoria',
            data: data,
            success: function(response) {
                $('#table-categorias').html(response);
                $('#novaCategoriaModal').modal('hide');

                // Alerta de sucesso

            },
            error: function(xhr, status, error) {
                if (xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                    
                    for (var campo in errors) {
                        if (errors.hasOwnProperty(campo)) {
                            var mensagem = errors[campo][0]; 
                            $('#nomeCategoria').addClass('is-invalid'); 
                            $('#nomeCategoria').siblings('.invalid-feedback').html(mensagem);
                        }
                    }
                } else {
                    // Alerta para erros

                }
            }
        });
    }

    function limparModal()
    {
        document.getElementById('nomeCategoria').value = '';
        $('#nomeCategoria').removeClass('is-invalid'); 
        $('#nomeCategoria').siblings('.invalid-feedback').html(''); 

    }

    function modalExcluir(id, nome)
    {
        document.getElementById('nomeCategoriaExcluir').innerHTML = nome;
        document.getElementById('id_categoria').value = id;
        $('#excluirCategoriaModal').modal('show');
    }

    function excluir()
    {
        var data = {
            _method: 'delete',
            _token: document.getElementById('token').value
        };

        var id_categoria = document.getElementById('id_categoria').value;

        $.ajax({
            type: 'POST',
            url: '/categoria/' + id_categoria,
            data: data,
            success: function(response) {
                $('#table-categorias').html(response);
                $('#excluirCategoriaModal').modal('hide');

                // Alerta de sucesso

            },
            error: function(xhr, status, error) {
                // Alerta de erros
            }
        });
    }

</script>

@endsection