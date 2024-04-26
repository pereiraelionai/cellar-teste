<div class="mt-3" style="display: flex; align-items: center; justify-content: center;">
    <ul class="pagination">
        @if($data->onFirstPage())
            <li class="page-item disabled"><span class="page-link">Anterior</span></li>
        @else
            <li class="page-item"><a class="page-link" href="{{ $data->previousPageUrl() }}">Anterior</a></li>
        @endif

        @foreach ($data->getUrlRange(1, $data->lastPage()) as $page => $url)
            @if ($page == $data->currentPage())
                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
            @endif
        @endforeach

        @if($data->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $data->nextPageUrl() }}">Próximo</a></li>
        @else
            <li class="page-item disabled"><span class="page-link">Próximo</span></li>
        @endif
    </ul>
</div>