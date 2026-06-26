<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label gc-required">Nome</label>
        <input type="text" name="nome" value="{{ old('nome', $usuario->nome ?? '') }}"
               class="form-control @error('nome') is-invalid @enderror">
        @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label gc-required">Login</label>
        <input type="text" name="login" value="{{ old('login', $usuario->login ?? '') }}"
               class="form-control @error('login') is-invalid @enderror">
        @error('login') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label gc-required">Perfil</label>
        <select name="perfil_id" class="form-select @error('perfil_id') is-invalid @enderror">
            <option value="">Selecione</option>
            @foreach($perfis as $perfil)
                <option value="{{ $perfil->id }}" @selected(old('perfil_id', $usuario->perfil_id ?? '') == $perfil->id)>
                    {{ $perfil->nome }}
                </option>
            @endforeach
        </select>
        @error('perfil_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label gc-required">E-mail</label>
        <input type="email" name="email" value="{{ old('email', $usuario->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label {{ $usuario ? '' : 'gc-required' }}">
            Senha {{ $usuario ? '(deixe em branco para manter)' : '' }}
        </label>
        <input type="password" name="senha" class="form-control @error('senha') is-invalid @enderror">
        @error('senha') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label {{ $usuario ? '' : 'gc-required' }}">Confirmar senha</label>
        <input type="password" name="senha_confirmation" class="form-control">
    </div>

    @if($usuario)
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1"
                       @checked(old('ativo', $usuario->ativo)) >
                <label class="form-check-label" for="ativo">Usuário ativo</label>
            </div>
        </div>
    @endif
</div>
