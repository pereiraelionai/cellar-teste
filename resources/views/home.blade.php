@extends('layouts.app')

@section('content')

<!-- Tabela Principal -->
<div class="main-content">
    <h2>Resumo dos dados</h2>
    <div id="graficos">
        <div class="row mt-3">
            <div class="col-lg">
              <div class="card card-stats mb-4 mb-xl-0" style="height: 130px;">
                <div class="card-body card-dash">
                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-2">Categorias Cadastradas</h5>
                      <span class="h3 font-weight-bold mb-0">{{ $data['categorias'] }}</span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                      <i class="bi bi-tags-fill icon-dash"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>      
            <div class="col-lg">
              <div class="card card-stats mb-4 mb-xl-0" style="height: 130px;">
                <div class="card-body card-dash">
                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-2">Produtos Cadastrados</h5>
                      <span class="h3 font-weight-bold mb-0">{{ $data['produtos'] }}</span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                        <i class="bi bi-box-seam-fill icon-dash"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>  
            <div class="col-lg">
              <div class="card card-stats mb-4 mb-xl-0" style="height: 130px;">
                <div class="card-body card-dash">
                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-2">Usuários Ativos</h5>
                      <span class="h3 font-weight-bold mb-0">{{ $data['usuarios'] }}</span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                        <i class="bi bi-people-fill icon-dash"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>                             
      </div>
</div>
@endsection
