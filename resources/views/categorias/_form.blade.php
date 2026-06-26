<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label gc-required">Nome</label>
        <input type="text" name="nome" value="{{ old('nome', $categoria->nome ?? '') }}"
               class="form-control @error('nome') is-invalid @enderror">
        @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" rows="3" class="form-control @error('descricao') is-invalid @enderror">{{ old('descricao', $categoria->descricao ?? '') }}</textarea>
        @error('descricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    @if($categoria)
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1"
                       @checked(old('ativo', $categoria->ativo)) >
                <label class="form-check-label" for="ativo">Categoria ativa</label>
            </div>
        </div>
    @endif
</div>
