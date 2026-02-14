@extends('layouts.asana')

@section('content')
<style>
    body {
        background: #0f1012 !important;
        min-height: 100vh;
        color: white;
    }
    .attendance-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .status-in { color: #22c55e; }
    .status-out { color: #f97316; }
</style>

<div class="py-12 px-8 max-w-[1000px] mx-auto">
    
    <div class="flex justify-between items-end mb-12">
        <div>
            <span class="text-orange-500 text-xs font-black uppercase tracking-[0.4em] mb-4 block">Reportes Diarios</span>
            <h1 class="text-3xl font-black text-white uppercase italic tracking-tighter">Historial de Asistencia<span class="text-orange-500 not-italic">.</span></h1>
        </div>
        <a href="{{ route('attendance.scanner') }}" class="px-6 py-3 bg-white text-black font-bold text-xs uppercase tracking-widest rounded-xl hover:bg-gray-200 transition-all flex items-center gap-2">
            <i class="fas fa-qrcode"></i> Abrir Scanner
        </a>
    </div>

    <div class="attendance-card rounded-[2rem] overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Colaborador</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Entrada</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Salida</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($attendances as $record)
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500 font-bold">
                                {{ substr($record->teamMember->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">{{ $record->teamMember->name }}</p>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest">{{ $record->teamMember->position }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-sm font-medium text-gray-300">{{ $record->check_in->format('H:i:s') }}</span>
                        <p class="text-[10px] text-gray-600">{{ $record->check_in->format('d M, Y') }}</p>
                    </td>
                    <td class="px-8 py-6">
                        @if($record->check_out)
                            <span class="text-sm font-medium text-gray-300">{{ $record->check_out->format('H:i:s') }}</span>
                        @else
                            <span class="text-xs font-bold text-gray-600 uppercase tracking-widest italic">En oficina</span>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        @if($record->status == 'late')
                            <span class="px-3 py-1 rounded-full bg-red-500/10 text-red-500 text-[10px] font-black uppercase tracking-widest border border-red-500/20">Tardanza</span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-green-500/10 text-green-500 text-[10px] font-black uppercase tracking-widest border border-green-500/20">A tiempo</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center">
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">No hay registros de asistencia hoy.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        {{ $attendances->links() }}
    </div>

</div>
@endsection
