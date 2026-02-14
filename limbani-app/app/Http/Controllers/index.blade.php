<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- NAVEGACIÓN TIPO PESTAÑAS -->
            <div class="flex space-x-8 border-b border-gray-200 mb-8">
                <a href="{{ route('dashboard') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 transition-colors">
                    Campañas Activas
                </a>
                <a href="{{ route('team.index') }}" class="border-b-2 border-orange-500 py-4 px-1 text-sm font-bold text-orange-600">
                    Equipo & Nómina
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Gestión de Talento Humano</h3>
                        <p class="text-sm text-gray-500">Administra la información de tus colaboradores, cargos y nómina.</p>
                    </div>
                    
                    <!-- Botón para abrir modal (usando AlpineJS simple) -->
                    <div x-data="{ open: false }">
                        <button @click="open = true" class="bg-gray-900 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-full shadow-lg transition flex items-center gap-2 text-sm">
                            <i class="fas fa-user-plus"></i> Nuevo Colaborador
                        </button>

                        <!-- MODAL DE REGISTRO -->
                        <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="open = false">
                                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                </div>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <form action="{{ route('team.store') }}" method="POST" class="p-6">
                                        @csrf
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Registrar Nuevo Talento</h3>
                                        
                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                                                <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Cédula / ID</label>
                                                <input type="text" name="cedula" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                                                <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Cargo en la Agencia</label>
                                                <input type="text" name="position" placeholder="Ej: Diseñador Senior, Copywriter..." required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Salario Mensual</label>
                                                <input type="number" step="0.01" name="salary" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Información Bancaria</label>
                                                <textarea name="bank_details" rows="2" placeholder="Banco, Tipo de Cuenta, Número..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-end gap-2 mt-6">
                                            <button type="button" @click="open = false" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
                                            <button type="submit" class="bg-orange-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-orange-700">Guardar Ficha</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLA DE COLABORADORES -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Colaborador</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto & ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datos Bancarios</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Salario</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($team as $member)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-900 rounded-full flex items-center justify-center text-white font-bold text-xs">
                                                {{ substr($member->name, 0, 2) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $member->name }}</div>
                                                <div class="text-xs text-green-600 font-semibold">Activo</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $member->position }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><i class="fas fa-id-card text-gray-400 mr-1"></i> {{ $member->cedula }}</div>
                                        <div class="text-sm text-gray-500"><i class="fas fa-phone text-gray-400 mr-1"></i> {{ $member->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500 max-w-xs truncate">{{ $member->bank_details ?? 'No registrado' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                        ${{ number_format($member->salary, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No hay colaboradores registrados aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>