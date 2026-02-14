<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-bold mb-4">Nueva Campaña Publicitaria</h2>
                
                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700">Nombre de la Campaña</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded-lg" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Descripción / Objetivo</label>
                        <textarea name="description" class="w-full border-gray-300 rounded-lg"></textarea>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Crear Proyecto</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>