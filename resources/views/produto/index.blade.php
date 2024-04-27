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
        @if(session('permissao')->criar_editar)
            <button class="btn btn-success" onclick="modalSalvar()">Novo Produto</button>     
        @endif                  
        </div>
    </div>

    <div class="card">
        <div class="card-body" id="table-produtos">
        @include('produto/table')
        </div>
    </div>
</div>

<!-- Modal cadastrar/editar produto -->
<div class="modal fade" id="produtoModal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Produto</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <div class="row">
                <div class="col-md">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" class="form-control">
                    <div id="nomeError" class="invalid-feedback"></div>
                </div>
            </div>

            <div class="row mt-2">
            <div class="col-md">
                <label for="valor">Valor</label>
                <input type="text" id="valor" class="form-control">
                <div id="valorError" class="invalid-feedback"></div>
            </div>
            <div class="col-md">
                <label for="categoria">Categoria</label>
                <select id="categoria" class="form-select">
                    <option value="" selected disabled>Selecione</option>
                    @foreach($categorias as $item)
                    <option value="{{ $item->id }}">{{ $item->nome }}</option>
                    @endforeach
                </select>
                <div id="categoriaError" class="invalid-feedback"></div>
            </div>
        </div>

            <input type="hidden" id="token" value="{{ csrf_token() }}">
       </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="button" id="btnModalProduto"></button>
      </div>
    </div>
  </div>
</div>

<!-- Modal excluir produto -->
@include('layouts/modalExcluir', ['tipo' => 'Produto'])

<!-- Alerta de erro -->
@include('layouts/alerta')

<!-- Id produto para o update e destroy -->
<input type="hidden" id="id_produto">

@endsection

@section('js')

<script>

    function modalSalvar()
    {   
        // Limpar form
        limparModal();
        // Setando btn para salvar
        $('#btnModalProduto').off('click').removeClass().addClass('btn btn-success').text('Salvar').click(salvar);
        // Abrir modal
        $('#produtoModal').modal('show');
    }

    function salvar()
    {
        var data = {
            nome: document.getElementById('nome').value,
            valor: document.getElementById('valor').value,
            categoria: document.getElementById('categoria').value,
            _token: document.getElementById('token').value
        };

        $.ajax({
            type: 'POST',
            url: '/produto',
            data: data,
            success: function(response) {
                $('#table-produtos').html(response);
                $('#produtoModal').modal('hide');

            },
            error: function(xhr, status, error) {
                
                if (xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;

                    // Limpa todos os campos de erro antes de adicionar novos
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').html('');
    
                    for (var campo in errors) {
                        if (errors.hasOwnProperty(campo)) {
                            var mensagem = errors[campo][0]; 
                            $('#' + campo).addClass('is-invalid'); 
                            $('#' + campo).siblings('.invalid-feedback').html(mensagem);
                        }
                    }
                } else {
                    $('#produtoModal').modal('hide');
                    document.getElementById('msg-toast').innerHTML = xhr.responseJSON.message
                    var toast = document.getElementById('liveToast');
                    var bsToast = new bootstrap.Toast(toast);
                    bsToast.show();

                }
            }
        });
    }

    function modalEditar(id, nome, valor, categoria)
    {   
        // Limpar form
        limparModal();
        
        // Setando dados no form
        document.getElementById('nome').value = nome;
        document.getElementById('valor').value = formatarMoeda(valor);
        document.getElementById('categoria').value = categoria;
        document.getElementById('id_produto').value = id;

        // Setando btn para editar
        $('#btnModalProduto').off('click').removeClass().addClass('btn btn-warning').text('Editar').click(editar);

        // Abrir modal
        $('#produtoModal').modal('show');
    }

    function editar()
    {
        var data = {
            nome: document.getElementById('nome').value,
            valor: document.getElementById('valor').value,
            categoria: document.getElementById('categoria').value,
            _method: 'put',
            _token: document.getElementById('token').value
        };

        var id_categoria = document.getElementById('id_produto').value;

        $.ajax({
            type: 'POST',
            url: '/produto/' + id_categoria,
            data: data,
            success: function(response) {
                $('#table-produtos').html(response);
                $('#produtoModal').modal('hide');

            },
            error: function(xhr, status, error) {
                if (xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;

                    // Limpa todos os campos de erro antes de adicionar novos
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').html('');
    
                    for (var campo in errors) {
                        if (errors.hasOwnProperty(campo)) {
                            var mensagem = errors[campo][0]; 
                            $('#' + campo).addClass('is-invalid'); 
                            $('#' + campo).siblings('.invalid-feedback').html(mensagem);
                        }
                    }
                } else {
                    $('#produtoModal').modal('hide');
                    document.getElementById('msg-toast').innerHTML = xhr.responseJSON.message
                    var toast = document.getElementById('liveToast');
                    var bsToast = new bootstrap.Toast(toast);
                    bsToast.show();

                }
            }
        });
    }    

    function modalExcluir(id, nome)
    {
        document.getElementById('nomeExcluir').innerHTML = nome;
        document.getElementById('id_produto').value = id;
        $('#excluirModal').modal('show');
    }

    function excluir()
    {
        var data = {
            _method: 'delete',
            _token: document.getElementById('token').value
        };

        var id_categoria = document.getElementById('id_produto').value;

        $.ajax({
            type: 'POST',
            url: '/produto/' + id_categoria,
            data: data,
            success: function(response) {
                $('#table-produtos').html(response);
                $('#excluirModal').modal('hide');

            },
            error: function(xhr, status, error) {
                $('#excluirModal').modal('hide');
                document.getElementById('msg-toast').innerHTML = xhr.responseJSON.message
                var toast = document.getElementById('liveToast');
                var bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            }
        });
    }

    function limparModal()
    {
        // Limpa todos os campos de erro
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');
        
        // Limpando formulario
        document.getElementById('nome').value = '';
        document.getElementById('valor').value = '';
        Mascara.setMoeda('valor');
        document.getElementById('categoria').value = ''
    }    

    

</script>

@endsection