@extends('layouts.admin')

@section('content')
    <h1>Crear rol</h1>
    <hr>

    <div class="card">
        <div class="card-header"><h4>Datos del rol</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                {{-- Nombre --}}
                <div class="mb-4">
                    <label class="form-label">Nombre del rol</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               placeholder="Nombre del rol" value="{{ old('name') }}">
                        @error('name')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Tabla de permisos --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Permisos por módulo</label>

                    {{-- Botones de selección masiva --}}
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAll(true)">
                            <i class="bi bi-check-all"></i> Seleccionar todo
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAll(false)">
                            <i class="bi bi-x-lg"></i> Deseleccionar todo
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width:180px">Módulo</th>
                                    @foreach($actions as $action => $label)
                                        <th class="text-center">{{ $label }}</th>
                                    @endforeach
                                    <th class="text-center">Todos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modules as $module => $moduleLabel)
                                    <tr>
                                        <td class="fw-semibold">{{ $moduleLabel }}</td>
                                        @foreach($actions as $action => $label)
                                            @php $permName = "{$module}.{$action}"; @endphp
                                            <td class="text-center">
                                                @if($permissions->has($module) && $permissions[$module]->firstWhere('name', $permName))
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input perm-check perm-{{ $module }}"
                                                               type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $permName }}"
                                                               {{ old('permissions') && in_array($permName, old('permissions', [])) ? 'checked' : '' }}>
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        {{-- Checkbox "Todos" por fila --}}
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                       onchange="toggleRow('{{ $module }}', this.checked)">
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Registrar</button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleAll(checked) {
            document.querySelectorAll('.perm-check').forEach(cb => cb.checked = checked);
        }
        function toggleRow(module, checked) {
            document.querySelectorAll('.perm-' + module).forEach(cb => cb.checked = checked);
        }
    </script>
    @endpush
@endsection