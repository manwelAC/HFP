<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class DisciplinaryController extends Controller
{
    public function index()
    {
        return view("disciplinary.disciplinary");
    }

    // Load DA list for DataTable
    public function list(Request $request)
    {
        $user = Auth::user();
        $user_type = $user->access['disciplinary']['user_type'] ?? null;

        $das = DB::table('tbl_disciplinary_action as da')
            ->leftJoin('tbl_employee as e', 'da.employee_id', '=', 'e.id')
            ->leftJoin('tbl_nte as n', 'da.nte_id', '=', 'n.id')
            ->select(
                'da.id',
                'da.case_number',
                'da.sanction',
                DB::raw("DATE_FORMAT(da.date_issued, '%M %d, %Y') as date_issued"),
                'n.case_number as nte_case_number',
                DB::raw("CONCAT(e.first_name, ' ', e.last_name) as employee_name")
            );

        // If employee, only show their own DA
        if($user_type == 'employee'){
            $employee = DB::table('tbl_employee')->where('user_id', $user->id)->first();
            if($employee){
                $das = $das->where('da.employee_id', $employee->id);
            } else {
                return response()->json(['data' => []]);
            }
        }

        $das = $das->orderBy('da.date_created', 'desc')->get();

        $count = 1;
        foreach($das as $da){
            $da->DT_RowIndex = $count++;
            $da->action = $this->actionButtons($da->id);
        }

        return response()->json(['data' => $das]);
    }

    // Store new DA
    public function store(Request $request)
    {
        try {
            // Get parent NTE to derive case number
            $nte = DB::table('tbl_nte')->where('id', $request->nte_id)->first();

            // Derive DA case number from NTE case number
            // NTE-20260903-0001 → DA-20260903-0001
            $base = substr($nte->case_number, 4); // remove "NTE-"
            $da_case_number = "DA-" . $base;

            // Check if DA already exists for this NTE
            $existing = DB::table('tbl_disciplinary_action')
                ->where('nte_id', $request->nte_id)
                ->first();

            if($existing){
                return response()->json([
                    'success' => false,
                    'message' => 'Disciplinary Action already exists for this NTE!'
                ]);
            }

            DB::table('tbl_disciplinary_action')->insert([
                'case_number'     => $da_case_number,
                'nte_id'          => $request->nte_id,
                'employee_id'     => $request->employee_id,
                'case_details'    => $request->case_details,
                'remarks'         => $request->remarks,
                'sanction'        => $request->sanction,
                'sanction_details'=> $request->sanction_details,
                'date_issued'     => $request->date_issued,
                'date_created'    => Carbon::now(),
                'user_id_added'   => Auth::id(),
            ]);

            // Update NTE status to closed
            DB::table('tbl_nte')->where('id', $request->nte_id)->update([
                'status' => 'closed'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Disciplinary Action {$da_case_number} issued successfully!"
            ]);

        } catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }

    // View DA details
    public function view($id)
    {
        $da = DB::table('tbl_disciplinary_action as da')
            ->leftJoin('tbl_employee as e', 'da.employee_id', '=', 'e.id')
            ->leftJoin('tbl_nte as n', 'da.nte_id', '=', 'n.id')
            ->where('da.id', $id)
            ->select(
                'da.*',
                'n.case_number as nte_case_number',
                DB::raw("CONCAT(e.first_name, ' ', e.last_name) as employee_name")
            )
            ->first();

        return response()->json([
            'success' => true,
            'data'    => $da
        ]);
    }

    // Delete DA
    public function delete($id)
    {
        try {
            // Get the DA to find the NTE
            $da = DB::table('tbl_disciplinary_action')->where('id', $id)->first();

            // Re-open the NTE back to replied or pending
            DB::table('tbl_nte')->where('id', $da->nte_id)->update([
                'status' => 'replied'
            ]);

            DB::table('tbl_disciplinary_action')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Disciplinary Action deleted successfully!'
            ]);

        } catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }

    // Generate action buttons
    private function actionButtons($id)
    {
        $user = Auth::user();
        $access = $user->access['disciplinary']['access'] ?? '';
        $user_type = $user->access['disciplinary']['user_type'] ?? '';

        $buttons = '';

        if(preg_match("/R/i", $access)){
            $buttons .= '<button class="btn btn-info btn-sm btn_view_da" data-id="'.$id.'">
                            <i class="fa fa-eye"></i>
                         </button> ';
        }

        // Only HR can delete
        if($user_type == 'hr' && preg_match("/D/i", $access)){
            $buttons .= '<button class="btn btn-danger btn-sm btn_delete_da" data-id="'.$id.'">
                            <i class="fa fa-trash"></i>
                         </button>';
        }

        return $buttons;
    }
}