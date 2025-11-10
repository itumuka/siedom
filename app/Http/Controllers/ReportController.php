<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getMahasiswaJawabanChart()
    {
        try {
            $totalStudents = DB::table('akd_mahasiswa')->count();
            $completedStudents = DB::table('edom_jawaban')->distinct('user_id')->count('user_id');
            $notCompletedStudents = $totalStudents - $completedStudents;

            return response()->json([
                'completed_students' => $completedStudents,
                'not_completed_students' => $notCompletedStudents,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }
}
