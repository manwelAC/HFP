<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class dashboardController extends Controller
{
    public function dashboard()
    {
        $tbl_employee = count(DB::connection("intra_payroll")->table("tbl_employee")->where("is_active", 1)->get());
        $department = count(DB::connection("intra_payroll")->table("tbl_employee")->select("department")->where("is_active", 1)->groupBy('department')->get());

        $files = count(DB::connection("intra_payroll")->table("tbl_file")->get());
        $loans = count(DB::connection("intra_payroll")->table("tbl_loan_file")->where("is_done", 0)->get());
        $branches = count(DB::connection("intra_payroll")->table("tbl_employee")->select("branch_id")->where("is_active", 1)->groupBy('branch_id')->get());
        $leave_total = 0;
        $payroll_processing = 0;
        $payroll_done = 0;
        $logs = 0;
        $todayBirthdays = collect();
        $upcomingBirthdays = collect();

        if (Auth::user()->access["dashboard"]['user_type'] == "employee") {
            $leave_count = DB::connection("intra_payroll")->table("tbl_leave_used")->where("emp_id", Auth::user()->company["linked_employee"]["id"])->where("leave_status", "APPROVED")->where("leave_year", date("Y"))->sum("leave_count");
            $leave_total = DB::connection("intra_payroll")->table("tbl_leave_credits")->where("emp_id", Auth::user()->company["linked_employee"]["id"])->where("year_given", date("Y"))->sum("leave_count");
            $payroll_processing = count(DB::connection("intra_payroll")->table("tbl_payroll")->where("employee", "LIKE", "%|" . Auth::user()->company["linked_employee"]["id"] . "|%")->where("payroll_status", "!=", "CLOSED")->get());
            $payroll_done = count(DB::connection("intra_payroll")->table("tbl_payroll")->where("employee", "LIKE", "%|" . Auth::user()->company["linked_employee"]["id"] . "|%")->where("payroll_status", "CLOSED")->get());
            $logs = count(DB::connection("intra_payroll")->table("tbl_raw_logs")->where("biometric_id", Auth::user()->company["linked_employee"]["bio_id"])->where("logs", "LIKE", date("Y-m-d") . "%")->get());
        } else {
            $leave_count = count(DB::connection("intra_payroll")->table("tbl_leave_used")->whereRaw("'" . date("Y-m-d") . "' BETWEEN leave_date_from and leave_date_to and leave_status = 'APPROVED'")->get());
        }

        $today = Carbon::today();

        $allEmployees = DB::connection("intra_payroll")
            ->table("tbl_employee")
            ->select('id', 'first_name', 'middle_name', 'last_name', 'ext_name', 'date_of_birth', 'profile_picture', 'department')
            ->where('is_active', 1)
            ->whereNotNull('date_of_birth')
            ->get();

        $todayBirthdays    = collect();
        $upcomingBirthdays = collect();

        foreach ($allEmployees as $emp) {
            try {
                $dob = Carbon::createFromFormat('Y-m-d', $emp->date_of_birth);
            } catch (\Exception $e) {
                try {
                    $dob = Carbon::createFromFormat('m/d/Y', $emp->date_of_birth);
                } catch (\Exception $e2) {
                    continue;
                }
            }

            try {
                $birthdayThisYear = Carbon::create($today->year, $dob->month, $dob->day);
            } catch (\Exception $e) {
                $birthdayThisYear = Carbon::create($today->year, 3, 1);
            }

            $diffDays = $today->diffInDays($birthdayThisYear, false);

            if ((int)$diffDays === 0) {
                $emp->days_until = 0;
                $emp->full_name  = $this->formatEmployeeName($emp);
                $emp->age        = $dob->diffInYears($today);
                $todayBirthdays->push($emp);
            } elseif ($diffDays > 0 && $diffDays <= 7) {
                $emp->days_until = (int)$diffDays;
                $emp->full_name  = $this->formatEmployeeName($emp);
                $emp->age        = $dob->diffInYears($today) + 1;
                $upcomingBirthdays->push($emp);
            }
        }

        $upcomingBirthdays = $upcomingBirthdays->sortBy('days_until')->values();

        return view("dashboard.index")
            ->with("tbl_employee", $tbl_employee)
            ->with("department", $department)
            ->with("leave_count", $leave_count)
            ->with("leave_total", $leave_total)
            ->with("payroll_done", $payroll_done)
            ->with("payroll_processing", $payroll_processing)
            ->with("logs", $logs)
            ->with("files", $files)
            ->with("loans", $loans)
            ->with("branches", $branches)
            ->with("todayBirthdays", $todayBirthdays)
            ->with("upcomingBirthdays", $upcomingBirthdays);
    }

    private function formatEmployeeName($emp): string
    {
        $first  = trim($emp->first_name ?? '');
        $middle = trim($emp->middle_name ?? '');
        $last   = trim($emp->last_name ?? '');
        $ext    = trim($emp->ext_name ?? '');

        $firstPart = $middle !== '' ? "{$first} {$middle}" : $first;
        $fullName  = "{$last}, {$firstPart}";

        if ($ext !== '') {
            $fullName .= " {$ext}";
        }

        return $fullName;
    }

    public function branch_per_emp()
    {
        $tbl_employee = DB::connection("intra_payroll")->table("tbl_employee")
            ->select("tbl_branch.branch as name", DB::raw("COUNT(tbl_employee.id) as y"))
            ->join("tbl_branch", "tbl_branch.id", "=", "branch_id")
            ->where("tbl_employee.is_active", 1)
            ->groupBy("branch_id")
            ->get();

        return json_encode($tbl_employee);
    }

    public function count_mwe()
    {
        $tbl_employee = DB::connection("intra_payroll")->table("tbl_employee")
            ->select(DB::raw("IF(is_mwe = 1, 'MWE', 'NON-MWE') as name"), DB::raw("COUNT(tbl_employee.id) as y"))
            ->where("tbl_employee.is_active", 1)
            ->groupBy("is_mwe")
            ->get();

        return json_encode($tbl_employee);
    }
}