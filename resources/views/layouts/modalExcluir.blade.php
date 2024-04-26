<div class="modal fade" id="excluirModal" aria-hidden="true" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Excluir {{ $tipo }}</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <p>Tem certeza que deseja excluir <strong id="nomeExcluir"></strong>?</p>
            <input type="hidden" id="token" value="{{ csrf_token() }}">
       </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-danger" onclick="excluir()">Excluir</button>
      </div>
    </div>
  </div>
</div>