@extends('layouts.admin')

@section('content')
    <h1>Editar rol: {{ $role->name }}</h1>
    <hr>

    <div class="card">
        <div class="card-header"><h4>Datos del rol</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="form-label">Nombre del rol</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $role->name) }}">
                        @error('name')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Permisos por módulo</label>
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
                                    @php
                                        // ¿Todos los permisos de este módulo están asignados?
                                        $modulePerms = array_keys($actions);
                                        $allChecked  = collect($modulePerms)->every(
                                            fn($a) => in_array("{$module}.{$a}", $rolePerms)
                                        );
                                    @endphp
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
                                                               {{ in_array($permName, old('permissions', $rolePerms)) ? 'checked' : '' }}>
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                       {{ $allChecked ? 'checked' : '' }}
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
                <button type="submit" class="btn btn-success">Actualizar</button>
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