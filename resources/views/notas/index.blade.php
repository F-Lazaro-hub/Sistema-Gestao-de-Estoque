@extends('layouts.app')

@section('title', 'Notas Internas')

@section('content')
<div class="container-fluid px-4">

    {{-- Cabeçalho --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold text-dark">
                <i class="bi bi-file-earmark-text me-2 text-primary"></i>Notas Internas
            </h1>
            <p class="text-muted mb-0 small">Registro e emissão de notas internas de movimentação</p>
        </div>
        <a href="{{ route('notas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Nova Nota
        </a>
    </div>

    {{-- Alertas flash --}}
    @if(session('sucesso'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('sucesso') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('erro'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle me-2"></i>{{ session('erro') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('notas.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-4 col-lg-3">
                    <label class="form-label form-label-sm fw-semibold">Número da Nota</label>
                    <input type="text"
                           name="busca"
                           value="{{ request('busca') }}"
                           class="form-control form-control-sm"
                           placeholder="NI-2025-0001">
                </div>
                <div class="col-sm-4 col-lg-2">
                    <label class="form-label form-label-sm fw-semibold">Data inicial</label>
                    <input type="date"
                           name="data_inicio"
                           value="{{ request('data_inicio') }}"
                           class="form-control form-control-sm">
                </div>
                <div class="col-sm-4 col-lg-2">
                    <label class="form-label form-label-sm fw-semibold">Data final</label>
                    <input type="date"
                           name="data_fim"
                           value="{{ request('data_fim') }}"
                           class="form-control form-control-sm">
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label form-label-sm fw-semibold">Responsável</label>
                    <input type="text"
                           name="responsavel"
                           value="{{ request('responsavel') }}"
                           class="form-control form-control-sm"
                           placeholder="Nome do responsável">
                </div>
                <div class="col-sm-6 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('notas.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($notas->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                    Nenhuma nota encontrada.
                    <a href="{{ route('notas.create') }}" class="d-block mt-2">Emitir a primeira nota</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Número</th>
                                <th>Data</th>
                                <th>Responsável</th>
                                <th>Itens</th>
                                <th class="text-end">Valor Total</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notas as $nota)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-semibold text-primary">{{ $nota->numero }}</span>
                                    </td>
                                    <td>{{ $nota->data_formatada }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width:32px;height:32px;min-width:32px">
                                                <span class="text-secondary fw-bold small">
                                                    {{ strtoupper(substr($nota->responsavel?->nome ?? '?', 0, 1)) }}
                                                </span>
                                            </div>
                                            <span class="small">{{ $nota->responsavel?->nome ?? 'N/D' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ $nota->itens_count ?? '—' }} itens
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold">{{ $nota->valor_total_formatado }}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('notas.show', $nota) }}"
                                               class="btn btn-outline-primary"
                                               title="Visualizar">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('notas.pdf', $nota) }}"
                                               class="btn btn-outline-danger"
                                               title="Baixar PDF"
                                               target="_blank">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                            <a href="{{ route('notas.imprimir', $nota) }}"
                                               class="btn btn-outline-secondary"
                                               title="Imprimir"
                                               target="_blank">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            <a href="{{ route('notas.edit', $nota) }}"
                                               class="btn btn-outline-warning"
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-outline-danger"
                                                    title="Excluir"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalExcluir"
                                                    data-nota-id="{{ $nota->id }}"
                                                    data-nota-numero="{{ $nota->numero }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginação --}}
                @if($notas->hasPages())
                    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                        <small class="text-muted">
                            Exibindo {{ $notas->firstItem() }}–{{ $notas->lastItem() }}
                            de {{ $notas->total() }} notas
                        </small>
                        {{ $notas->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- Modal de confirmação de exclusão --}}
<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="modalExcluirLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="modalExcluirLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir a nota <strong id="notaNumeroExcluir"></strong>?
                <br><small class="text-muted">Esta ação não pode ser desfeita.</small>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExcluir" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preenche o modal de exclusão com os dados da nota
    document.getElementById('modalExcluir').addEventListener('show.bs.modal', function (event) {
        const btn    = event.relatedTarget;
        const id     = btn.dataset.notaId;
        const numero = btn.dataset.notaNumero;

        document.getElementById('notaNumeroExcluir').textContent = numero;
        document.getElementById('formExcluir').action = `/notas/${id}`;
    });
</script>
@endpush