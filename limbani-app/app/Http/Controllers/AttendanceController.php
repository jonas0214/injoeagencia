<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('teamMember')
            ->orderBy('check_in', 'desc')
            ->paginate(20);
            
        return view('attendance.index', compact('attendances'));
    }

    public function scanner()
    {
        return view('attendance.scanner');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string|exists:team_members,cedula',
        ]);

        $member = TeamMember::where('cedula', $request->cedula)->first();
        $today = Carbon::today();

        // Buscar si ya tiene una entrada hoy sin salida
        $attendance = Attendance::where('team_member_id', $member->id)
            ->whereDate('check_in', $today)
            ->whereNull('check_out')
            ->first();

        if ($attendance) {
            // Registrar Salida
            $attendance->update([
                'check_out' => Carbon::now(),
            ]);
            return response()->json([
                'success' => true,
                'type' => 'out',
                'message' => 'Salida registrada correctamente',
                'member' => $member->name,
                'time' => Carbon::now()->format('H:i:s')
            ]);
        } else {
            // Registrar Entrada
            Attendance::create([
                'team_member_id' => $member->id,
                'check_in' => Carbon::now(),
                'status' => Carbon::now()->format('H:i') > '08:15' ? 'late' : 'present', // Ejemplo: tarde después de las 8:15
            ]);
            return response()->json([
                'success' => true,
                'type' => 'in',
                'message' => 'Entrada registrada correctamente',
                'member' => $member->name,
                'time' => Carbon::now()->format('H:i:s')
            ]);
        }
    }
}
