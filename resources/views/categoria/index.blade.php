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
            <button class="btn btn-success" onclick="modalSalvar()">Nova Categoria</button>                       
        </div>
    </div>

    <div class="card">
        <div class="card-body" id="table-categorias">
            @include('categoria/table')
        </div>
    </div>

</div>

<!-- Modal cadastrar/editar categoria -->
<div class="modal fade" id="categoriaModal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Categoria</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <label for="nome">Nome</label>
            <input type="text" id="nome" class="form-control">
            <div class="invalid-feedback"></div>
            <input type="hidden" id="token" value="{{ csrf_token() }}">
       </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="button" id="btnModalCategoria"></button>
      </div>
    </div>
  </div>
</div>

<!-- Modal excluir categoria -->
@include('layouts/modalExcluir', ['tipo' => 'Categoria'])

<!-- Id categoria para o update e destroy -->
<input type="hidden" id="id_categoria">

@endsection

@section('js')

<script>

    function modalSalvar()
    {   
        // Limpar form
        limparModal();
        // Setando btn para salvar
        $('#btnModalCategoria').off('click').removeClass().addClass('btn btn-success').text('Salvar').click(salvar);
        // Abrir modal
        $('#categoriaModal').modal('show');
    }

    function salvar()
    {
        var data = {
            nome: document.getElementById('nome').value,
            _token: document.getElementById('token').value
        };

        $.ajax({
            type: 'POST',
            url: '/categoria',
            data: data,
            success: function(response) {
                $('#table-categorias').html(response);
                $('#categoriaModal').modal('hide');

                // Alerta de sucesso

            },
            error: function(xhr, status, error) {
                if (xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                    
                    for (var campo in errors) {
                        if (errors.hasOwnProperty(campo)) {
                            var mensagem = errors[campo][0]; 
                            $('#nome').addClass('is-invalid'); 
                            $('#nome').siblings('.invalid-feedback').html(mensagem);
                        }
                    }
                } else {
                    // Alerta para erros

                }
            }
        });
    }

    function modalEditar(id, nome)
    {   
        // Limpar form
        limparModal();
        
        // Setando dados no form
        document.getElementById('nome').value = nome;
        document.getElementById('id_categoria').value = id;

        // Setando btn para editar
        $('#btnModalCategoria').off('click').removeClass().addClass('btn btn-warning').text('Editar').click(editar);

        // Abrir modal
        $('#categoriaModal').modal('show');
    }

    function editar()
    {
        var data = {
            nome: document.getElementById('nome').value,
            _method: 'put',
            _token: document.getElementById('token').value
        };

        var id_categoria = document.getElementById('id_categoria').value;

        $.ajax({
            type: 'POST',
            url: '/categoria/' + id_categoria,
            data: data,
            success: function(response) {
                $('#table-categorias').html(response);
                $('#categoriaModal').modal('hide');

                // Alerta de sucesso

            },
            error: function(xhr, status, error) {
                if (xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                    
                    for (var campo in errors) {
                        if (errors.hasOwnProperty(campo)) {
                            var mensagem = errors[campo][0]; 
                            $('#nome').addClass('is-invalid'); 
                            $('#nome').siblings('.invalid-feedback').html(mensagem);
                        }
                    }
                } else {
                    // Alerta para erros

                }
            }
        });
    }

    function modalExcluir(id, nome)
    {
        document.getElementById('nomeExcluir').innerHTML = nome;
        document.getElementById('id_categoria').value = id;
        $('#excluirModal').modal('show');
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
                $('#excluirModal').modal('hide');

                // Alerta de sucesso

            },
            error: function(xhr, status, error) {
                // Alerta de erros
            }
        });
    }

    function limparModal()
    {
        document.getElementById('nome').value = '';
        $('#nome').removeClass('is-invalid'); 
        $('#nome').siblings('.invalid-feedback').html(''); 

    }

</script>

@endsection