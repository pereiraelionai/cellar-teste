@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
        <div class="row mb-2">
            <div class="col div-titulo" id="titulo-head">
                <h1 class="h2">Usuários</h1>
            </div>
        </div>
        <div class="btn-toolbar mb-2">
            <button class="btn btn-success" onclick="modalSalvar()">Novo Usuário</button>                       
        </div>
    </div>

    <div class="card">
        <div class="card-body" id="table-usuarios">
        @include('usuario/table')
        </div>
    </div>
</div>

<!-- Modal cadastrar/editar produto -->
<div class="modal fade" id="usuarioModal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Usuário</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <div class="row">
                <div class="col-md">
                    <label for="name">Nome</label>
                    <input type="text" id="name" class="form-control">
                    <div id="nameError" class="invalid-feedback"></div>
                </div>
            </div>

            <div class="row mt-2" id="div-input-email">
                <div class="col-md">
                    <label for="email">Email</label>
                    <input type="email" id="email" class="form-control">
                    <div id="emailError" class="invalid-feedback"></div>
                </div>
            </div>

            <div id="div-permissoes">
                <div class="row mt-3">
                    <div class="col-md">
                        <strong for="menus" class="form-label">Modulos:</strong>
                        <br>
                        <div class="form-check form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="check_categorias">
                            <label class="form-check-label" for="check_categorias">Categorias</label>
                        </div>                                        
                        <div class="form-check form-check">
                            <input class="form-check-input" type="checkbox" id="check_produtos">
                            <label class="form-check-label" for="check_produtos">Produtos</label>
                        </div>
                    </div>     
                    
                    <div class="col-md">
                        <strong for="menus" class="form-label">Permissões:</strong>
                        <br>
                        <div class="form-check form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="check_criar_editar">
                            <label class="form-check-label" for="check_criar_editar">Criar / Editar</label>
                        </div>                                        
                        <div class="form-check form-check">
                            <input class="form-check-input" type="checkbox" id="check_excluir">
                            <label class="form-check-label" for="check_excluir">Excluir</label>
                        </div>
                    </div>  
                </div> 
                <div id="permissoesError" class="invalid-feedback"></div>
            </div>

            <input type="hidden" id="token" value="{{ csrf_token() }}">
       </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="button" id="btnModalUsuario"></button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ativar -->
<div class="modal fade" id="ativarModal" aria-hidden="true" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Ativar Usuário</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <p>Tem certeza que deseja ativar <strong id="nomeAtivar"></strong>?</p>
            <input type="hidden" id="token" value="{{ csrf_token() }}">
       </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-success" onclick="ativar()">Ativar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal excluir produto -->
@include('layouts/modalExcluir', ['tipo' => 'Usuario'])

<!-- Id produto para o update e destroy -->
<input type="hidden" id="id_usuario">


@endsection

@section('js')

<script>

    function modalSalvar()
    {   
        // Limpar form
        limparModal();

        // restaurando input email
        document.getElementById('div-input-email').style = 'display: block';
        
        // Verificando se o usuario altera permissoes para exibir os campos
        document.getElementById('div-permissoes').style = 'display: block';
        
        // Setando btn para salvar
        $('#btnModalUsuario').off('click').removeClass().addClass('btn btn-success').text('Salvar').click(salvar);
        // Abrir modal
        $('#usuarioModal').modal('show');
    }

    function salvar()
    {
        // Pegando permissoes
        const arrayPermissoes = {
            categoria: document.getElementById('check_categorias').checked,
            produto: document.getElementById('check_produtos').checked,
            criar_editar: document.getElementById('check_criar_editar').checked,
            excluir: document.getElementById('check_excluir').checked,
        }
        
        var data = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            permissoes: JSON.stringify(arrayPermissoes),
            _token: document.getElementById('token').value
        };

        document.getElementById('btnModalUsuario').disabled = true;
        
        $.ajax({
            type: 'POST',
            url: '/usuario',
            data: data,
            success: function(response) {
                $('#table-usuarios').html(response);
                $('#usuarioModal').modal('hide');

            },
            error: function(xhr, status, error) {
                document.getElementById('btnModalUsuario').disabled = false;
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
                    $('#usuarioModal').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: xhr.responseJSON.message,
                    });
                }
            }
        });

    }

    function modalEditar(usuario)
    {   
        // Limpar form
        limparModal();
        
        // Setando dados no form
        document.getElementById('name').value = usuario.name;
        document.getElementById('id_usuario').value = usuario.id;

        // ocultando input email
        document.getElementById('div-input-email').style = 'display: none';
        
        // Apenas usuarios tipo Usuario tem permissoes alteradas
        if(usuario.admin) {
            document.getElementById('div-permissoes').style = 'display: none';
        } else {
            document.getElementById('div-permissoes').style = 'display: block';
            // Set nas permissoes
            document.getElementById('check_categorias').checked = usuario.permissao.categorias;
            document.getElementById('check_produtos').checked = usuario.permissao.produtos;
            document.getElementById('check_criar_editar').checked = usuario.permissao.criar_editar;
            document.getElementById('check_excluir').checked = usuario.permissao.excluir;            
        }

        // Setando btn para editar
        $('#btnModalUsuario').off('click').removeClass().addClass('btn btn-warning').text('Editar').click(editar);

        // Abrir modal
        $('#usuarioModal').modal('show');
    }

    function editar()
    {   
        // Pegando permissoes
        const arrayPermissoes = {
            categoria: document.getElementById('check_categorias').checked,
            produto: document.getElementById('check_produtos').checked,
            criar_editar: document.getElementById('check_criar_editar').checked,
            excluir: document.getElementById('check_excluir').checked,
        }
        
        var data = {
            name: document.getElementById('name').value,
            permissoes: JSON.stringify(arrayPermissoes),
            _method: 'put',
            _token: document.getElementById('token').value
        };

        var id_usuario = document.getElementById('id_usuario').value;

        $.ajax({
            type: 'POST',
            url: '/usuario/' + id_usuario,
            data: data,
            success: function(response) {
                $('#table-usuarios').html(response);
                $('#usuarioModal').modal('hide');

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
                    $('#usuarioModal').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: xhr.responseJSON.message,
                    });

                }
            }
        });
    }    

    function modalExcluir(id, nome)
    {
        document.getElementById('nomeExcluir').innerHTML = nome;
        document.getElementById('id_usuario').value = id;
        $('#excluirModal').modal('show');
    }

    function excluir()
    {
        var data = {
            _method: 'delete',
            _token: document.getElementById('token').value
        };

        var id_usuario = document.getElementById('id_usuario').value;

        $.ajax({
            type: 'POST',
            url: '/usuario/' + id_usuario,
            data: data,
            success: function(response) {
                $('#table-usuarios').html(response);
                $('#excluirModal').modal('hide');

            },
            error: function(xhr, status, error) {
                $('#excluirModal').modal('hide');
                Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: xhr.responseJSON.message,
                    });
            }
        });
    }

    function modalAtivar(id, nome)
    {
        document.getElementById('nomeAtivar').innerHTML = nome;
        document.getElementById('id_usuario').value = id;
        $('#ativarModal').modal('show');
    }

    function ativar()
    {
        var data = {
            _method: 'patch',
            _token: document.getElementById('token').value
        };

        var id_usuario = document.getElementById('id_usuario').value;

        $.ajax({
            type: 'POST',
            url: '/usuario/ativar/' + id_usuario,
            data: data,
            success: function(response) {
                $('#table-usuarios').html(response);
                $('#ativarModal').modal('hide');

            },
            error: function(xhr, status, error) {
                $('#ativarModal').modal('hide');
                Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: xhr.responseJSON.message,
                    });
            }
        });
    }

    function limparModal()
    {
        // Limpa todos os campos de erro
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');
        document.getElementById('check_categorias').checked = false;
        document.getElementById('check_produtos').checked = false;
        document.getElementById('check_criar_editar').checked = false;
        document.getElementById('check_excluir').checked = false;
        
        // Limpando formulario
        document.getElementById('name').value = '';
        document.getElementById('email').value = '';
        document.getElementById('btnModalUsuario').disabled = false;
    }    

</script>

@endsection