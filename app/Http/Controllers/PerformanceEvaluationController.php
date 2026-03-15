<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerformanceEvaluationController extends Controller
{
    private $conn = 'intra_payroll';

    // ─── INDEX ───────────────────────────────────────────────────────────────
    public function index()
    {
        $employees = DB::connection($this->conn)
            ->table('tbl_employee')
            ->where('is_active', 1)
            ->orderBy('last_name')
            ->get();

        return view('performance_evaluation.performance_eval', compact('employees'));
    }

    // ─── LIST (DataTables AJAX) ───────────────────────────────────────────────
public function list(Request $request)
{
    $user = Auth::user();
    $isHR = $user->access['performance_evaluation']['user_type'] === 'hr';

    $query = DB::connection($this->conn)
        ->table('tbl_performance_evaluation as pe')
        ->join('tbl_employee as e', 'e.id', '=', 'pe.employee_id')
        ->select(
            'pe.id',
            DB::raw("CONCAT(e.last_name, ', ', e.first_name, ' ', COALESCE(e.middle_name,'')) as employee_name"),
            'pe.rating',
            'pe.date_served',
            'pe.attachment',
            'pe.date_created'
        );

    // Employees only see their own records
    if (!$isHR) {
        $query->where('pe.employee_id', $user->employee_id);
    }

    $records = $query->orderByDesc('pe.date_created')->get();

    return response()->json(['data' => $records]);
}

    // ─── STORE ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'employee_id'        => 'required|integer',
            'performance_details'=> 'required|string',
            'rating'             => 'required|in:outstanding,very_satisfactory,satisfactory,unsatisfactory',
            'date_served'        => 'required|date',
            'remarks'            => 'nullable|string',
            'attachment'         => 'nullable|file|max:20480', // 20 MB max
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $this->uploadFile($request->file('attachment'));
        }

        DB::connection($this->conn)->table('tbl_performance_evaluation')->insert([
            'employee_id'         => $request->employee_id,
            'performance_details' => $request->performance_details,
            'rating'              => $request->rating,
            'remarks'             => $request->remarks,
            'date_served'         => $request->date_served,
            'attachment'          => $attachmentPath,
            'date_created'        => Carbon::now(),
            'user_id_added'       => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Performance Evaluation saved successfully.']);
    }

    // ─── SHOW (for View modal) ────────────────────────────────────────────────
    public function show($id)
    {
        $record = DB::connection($this->conn)
            ->table('tbl_performance_evaluation as pe')
            ->join('tbl_employee as e', 'e.id', '=', 'pe.employee_id')
            ->select(
                'pe.*',
                DB::raw("CONCAT(e.last_name, ', ', e.first_name, ' ', COALESCE(e.middle_name,'')) as employee_name")
            )
            ->where('pe.id', $id)
            ->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $record]);
    }

    // ─── EDIT (for Edit modal pre-fill) ──────────────────────────────────────
    public function edit($id)
    {
        $record = DB::connection($this->conn)
            ->table('tbl_performance_evaluation')
            ->where('id', $id)
            ->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $record]);
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id'        => 'required|integer',
            'performance_details'=> 'required|string',
            'rating'             => 'required|in:outstanding,very_satisfactory,satisfactory,unsatisfactory',
            'date_served'        => 'required|date',
            'remarks'            => 'nullable|string',
            'attachment'         => 'nullable|file|max:20480',
        ]);

        $existing = DB::connection($this->conn)
            ->table('tbl_performance_evaluation')
            ->where('id', $id)
            ->first();

        if (!$existing) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        $attachmentPath = $existing->attachment; // keep old file by default

        if ($request->hasFile('attachment')) {
            // Delete old file if it exists
            if ($existing->attachment && file_exists(public_path($existing->attachment))) {
                unlink(public_path($existing->attachment));
            }
            $attachmentPath = $this->uploadFile($request->file('attachment'));
        }

        DB::connection($this->conn)->table('tbl_performance_evaluation')
            ->where('id', $id)
            ->update([
                'employee_id'         => $request->employee_id,
                'performance_details' => $request->performance_details,
                'rating'              => $request->rating,
                'remarks'             => $request->remarks,
                'date_served'         => $request->date_served,
                'attachment'          => $attachmentPath,
            ]);

        return response()->json(['success' => true, 'message' => 'Performance Evaluation updated successfully.']);
    }

    // ─── DESTROY ──────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $existing = DB::connection($this->conn)
            ->table('tbl_performance_evaluation')
            ->where('id', $id)
            ->first();

        if (!$existing) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        // Delete attached file if present
        if ($existing->attachment && file_exists(public_path($existing->attachment))) {
            unlink(public_path($existing->attachment));
        }

        DB::connection($this->conn)->table('tbl_performance_evaluation')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Record deleted successfully.']);
    }

    // ─── HELPER: Upload file to public/ ──────────────────────────────────────
    private function uploadFile($file)
    {
        $folder   = public_path('uploads/performance_evaluation');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $file->move($folder, $filename);

        return 'uploads/performance_evaluation/' . $filename;
    }
}