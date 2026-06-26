<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label gc-required">Código</label>
        <input type="text" name="codigo" value="{{ old('codigo', $produto->codigo ?? '') }}"
               class="form-control @error('codigo') is-invalid @enderror">
        @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-9">
        <label class="form-label gc-required">Nome</label>
        <input type="text" name="nome" value="{{ old('nome', $produto->nome ?? '') }}"
               class="form-control @error('nome') is-invalid @enderror">
        @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" rows="2" class="form-control @error('descricao') is-invalid @enderror">{{ old('descricao', $produto->descricao ?? '') }}</textarea>
        @error('descricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label gc-required">Categoria</label>
        <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror">
            <option value="">Selecione</option>
            @foreach($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected(old('categoria_id', $produto->categoria_id ?? '') == $categoria->id)>
                    {{ $categoria->nome }}
                </option>
            @endforeach
        </select>
        @error('categoria_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Marca</label>
        <input type="text" name="marca" value="{{ old('marca', $produto->marca ?? '') }}" class="form-control @error('marca') is-invalid @enderror">
        @error('marca') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label gc-required">Unidade</label>
        <input type="text" name="unidade" value="{{ old('unidade', $produto->unidade ?? '') }}" placeholder="ex.: UN, CX, KG"
               class="form-control @error('unidade') is-invalid @enderror">
        @error('unidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label gc-required">Quantidade mínima em estoque</label>
        <input type="number" step="0.01" min="0" name="quantidade_minima"
               value="{{ old('quantidade_minima', $produto->estoque->quantidade_minima ?? 0) }}"
               class="form-control @error('quantidade_minima') is-invalid @enderror">
        @error('quantidade_minima') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">Usada pelo módulo de Estoque para gerar alertas automáticos.</div>
    </div>

    @if($produto)
        <div class="col-md-4">
            <label class="form-label">Último valor pago</label>
            <input type="text" value="R$ {{ number_format($produto->ultimo_valor_pago ?? 0, 2, ',', '.') }}" class="form-control" disabled>
            <div class="form-text">Atualizado automaticamente pelas entradas de estoque.</div>
        </div>

        <div class="col-md-4">
            <label class="form-label">Valor médio</label>
            <input type="text" value="R$ {{ number_format($produto->valor_medio ?? 0, 2, ',', '.') }}" class="form-control" disabled>
        </div>

        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1"
                       @checked(old('ativo', $produto->ativo)) >
                <label class="form-check-label" for="ativo">Produto ativo</label>
            </div>
        </div>
    @endif
</div>
