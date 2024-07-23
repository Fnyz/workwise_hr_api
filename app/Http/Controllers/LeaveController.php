<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Leave;
use App\Models\User;
use App\Models\Notification;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\UpdateLeaveRequest;
use Illuminate\Http\Request;
use App\Mail\LeaveEmailSentStatus;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $department_head = $request->input("employee_id");
        $searchKeyword = $request->input('search');
     
        switch ($request->input('type')) {
            case 'leave_history':
                $data = DB::table('leaves as l')
                ->select(
                    'em.id',
                    'em.employee_id',
                    'em.employee_name',
                    'de.department',
                    'p.position',
                    'l.id as leave_id',
                    'm.employee_image',
                    'lt.leave_type',
                    'l.leave_status_date_time',
                    DB::raw("CONCAT(de.department, '-', m.employee_name, ' (', m.employee_role, ')') as approval_head"),
                    'l.leave_status'
                )
                ->leftJoin('employees as em', 'l.employee_id', '=', 'em.id')
                ->leftJoin('leave_types as lt', 'l.leave_type_id', '=', 'lt.id')
                ->leftJoin('departments as de', 'l.department_id', '=', 'de.id')
                ->leftJoin('employees as m', 'l.employee_approval_id', '=', 'm.id')
                ->leftJoin('positions as p', 'em.position_id', '=', 'p.id')
                ->when($searchKeyword, function ($query) use ($searchKeyword) {
                    return $query->where('em.employee_name', 'like', '%' . $searchKeyword . '%')
                                ->orWhere('l.leave_status', 'like', '%' . $searchKeyword . '%')
                                ->orWhere('de.department', 'like', '%' . $searchKeyword . '%')
                                ->orWhere('p.position', 'like', '%' . $searchKeyword . '%')
                                ->orWhere('lt.leave_type', 'like', '%' . $searchKeyword . '%');
                })
                ->orderBy('l.id', 'DESC')
                ->paginate(10)
                ->appends(['search' => $searchKeyword]);

            break;
            case 'department_request':
            
                $data = Leave::query()->select(
                    'departments.id AS department_id',
                    'employees.employee_id as employee_id',
                    'leaves.id AS leave_id',
                    'departments.department',
                    'employees.employee_name AS employee_name',
                    'employees.employee_image',
                    'leave_types.leave_type AS leave_type',
                    'leaves.leave_apply_date',
                    'leaves.leave_start_date',
                    'leaves.leave_end_date',
                    'leaves.leave_status',
                    'leaves.created_at',
                    'positions.position',
                    'employees.employee_role'
                )
                ->leftJoin('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
                ->join('employees', 'leaves.employee_id', '=', 'employees.id')
                ->join('positions', 'employees.position_id', '=', 'positions.id')
                ->join('departments', 'leaves.department_id', '=', 'departments.id')
                ->when($searchKeyword, function ($query) use ($searchKeyword, $department_head) {
                    $query->where(function($query) use ($searchKeyword) {
                        $query->where('leave_types.leave_type', 'like', '%' . $searchKeyword . '%')
                              ->orWhere('leaves.leave_status', 'like', '%' . $searchKeyword . '%')
                              ->orWhere('employees.employee_name', 'like', '%' . $searchKeyword . '%')
                              ->orWhere('departments.department', 'like', '%' . $searchKeyword . '%')
                              ->orWhere('positions.position', 'like', '%' . $searchKeyword . '%')
                              ->orWhere('employees.employee_id', 'like', '%' . $searchKeyword . '%');
                    });
                })->where('leaves.employee_approval_id', '=', $department_head)
                ->orderBy('leaves.id', 'desc')
                ->paginate(10)
                ->appends(['search' => $searchKeyword]);
                
            break;
            case 'single':
                $data = Leave::query()->select(
                    'l.id as leave_id',
                    'lt.leave_type as leave_type',
                    'l.leave_apply_date',
                    'l.leave_start_date',
                    'l.updated_at',
                    'l.leave_end_date',
                    'l.leave_reason',
                    'l.leave_to_time',
                    'l.leave_from_time',
                    'l.leave_status',
                    'l.created_at',
                    'em.employee_image',
                    'em.employee_name',
                    'l.leave_status_date_time'
                )
                ->from('leaves as l')
                ->leftJoin('leave_types as lt', 'l.leave_type_id', '=' , 'lt.id')
                ->leftJoin('employees as em', 'l.employee_approval_id', '=' , 'em.id')
                ->when($searchKeyword, function ($query) use ($searchKeyword) {
                    return $query->where('lt.leave_type', 'like', '%' . $searchKeyword . '%')
                                ->orWhere('l.leave_status', 'like', '%' . $searchKeyword . '%');
                })->where("l.employee_id", "=", $request->employee_id)
                ->orderBy('l.id', 'desc')
                ->paginate(10)
                ->appends(['search' => $searchKeyword]);
            break;
            case 'all':
                $data = Leave::query()->select(
                    'emp.employee_id as employee_id',
                    'emp.employee_name as employee_name',
                    'emp.employee_image',
                    'l.id as leave_id',
                    'l.leave_reason',
                    'lt.leave_type as leave_type',
                    'l.leave_apply_date',
                    'l.leave_start_date',
                    'l.leave_end_date',
                    'l.leave_status',
                    'l.created_at'
                )
                ->from('leaves as l')
                ->joinLeaveType()
                ->joinEmployee()
                ->when($searchKeyword, function ($query) use ($searchKeyword) {
                    return $query->where('lt.leave_type', 'like', '%' . $searchKeyword . '%')
                                ->orWhere('l.leave_status', 'like', '%' . $searchKeyword . '%')
                                ->orWhere('emp.employee_name', 'like', '%' . $searchKeyword . '%');
                })->where("l.employee_approval_id", "=", $request->employee_id)
                ->orderBy("created_at", "DESC")
                ->paginate(10)
                ->appends(['search' => $searchKeyword]);
            break;
            case 'all_pending_leave':
                $data = Leave::query()->select(
                    'emp.employee_id as employee_id',
                    'emp.employee_name as employee_name',
                    'l.id as leave_id',
                    'lt.leave_type as leave_type',
                    'l.leave_apply_date',
                    'l.leave_start_date',
                    'l.leave_end_date',
                    'l.leave_status',
                    'l.created_at'
                )
                ->from('leaves as l')
                ->joinLeaveType()
                ->joinEmployee()
                ->where(function($query){
                    $query->where("l.employee_approval_role", "=", "HR")
                          ->orWhere("l.employee_approval_role", "=", "ADMIN");
                })
                ->where("l.leave_status", "=", "PENDING")
                ->latest('l.created_at') 
                ->limit(5) 
                ->get();
            break;
            
            
            default:
            break;
        }
   

         if($request->input('type') === "leave_history" || $request->input('type') === "single" || $request->input('type') === "all" || $request->input("type") === "department_request"){
            foreach ($data as $dt) {
                $dt->employee_image = $dt->employee_image ? \URL::to($dt->employee_image) : null;
            };
         }

         return response()->json([
            'data' => $data,
        ], 200);
      
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeaveRequest $request)
    {
        //
            $datas = $request->validated();
            Leave::create($datas);

            $latestInserTedData = Leave::latest()->first();

            return response()->json([
            'message' => 'Leave is created successfully',
            'latest_leave_data' => $latestInserTedData
            ], 200);

    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        
        
        
        $leave = Leave::select(
            'leaves.employee_approval_role',
            'employees.employee_image',
            'employees.employee_name',
            'leaves.id',
            'leaves.department_id',
            'leaves.employee_id',
            'leaves.leave_type_id',
            'leaves.leave_apply_date',
            'leaves.leave_start_date',
            'leaves.leave_end_date',
            'leaves.leave_reason',
            'leaves.leave_status',
            'leaves.leave_from_time',
            'leaves.leave_to_time',
            'ty.leave_type',
            'leaves.employee_approval_id',
            DB::raw('CONCAT(de.department, " - ", em.employee_name, " (", em.employee_role, ")") as department_head')
        )
        ->leftJoin('leave_types as ty', 'leaves.leave_type_id', '=', 'ty.id')
        ->leftJoin('employees', 'leaves.employee_id', '=', 'employees.id')
        ->leftJoin('departments as de', 'leaves.department_id', '=', 'de.id')
        ->leftJoin('employees as em', 'leaves.employee_approval_id', '=', 'em.id')
        ->where('leaves.id', $id)
        ->first();
        


        $leave->employee_image = $leave->employee_image ?  \URL::to($leave->employee_image) : null;

        return response()->json([
            'data' => $leave
        ], 200);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveRequest $request, string $id)
    {
        //
        $data = $request->validated();

        
        $leave = Leave::find($id);

        if (!$leave) {
            return response()->json([
                'message' => 'Leave is not found',
            ], 404);
        }

 
        $leave->update($data);

      
        if ($leave->leave_status == "APPROVED") {
            $res = Leave::query()
                ->select(
                    'lt.leave_type as leave_type',
                    'm.employee_name as owner_name',
                    'm.employee_email as owner_email',
                    'em.employee_name as person_to_approved_name',
                    'em.employee_role as person_to_approved_role',
                    'p.position as person_to_approved_position',
                    'l.leave_apply_date',
                    'l.leave_start_date',
                    'l.leave_end_date',
                    'l.leave_status'
                )
                ->from('leaves as l')
                ->leftJoin('leave_types as lt', 'l.leave_type_id', '=', 'lt.id')
                ->leftJoin('employees as m', 'l.employee_id', '=', 'm.id')
                ->leftJoin('employees as em', 'l.employee_approval_id', '=', 'em.id')
                ->leftJoin('positions as p', 'em.position_id', '=', 'p.id')
                ->where("l.id", "=", $leave->id)
                ->first(); 
            
                $res->leave_start_date = Carbon::parse($res->leave_start_date)->format('F d, Y');
                $res->leave_end_date = Carbon::parse($res->leave_end_date)->format('F d, Y');

             Mail::to($res->owner_email)->send(new LeaveEmailSentStatus($res));
        }

        return response()->json([
            'message' => 'Leave is updated successfully',
            'data' => $leave,
        ], 200);
        

        
        

        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $leave = Leave::find($id);

         if(!$leave){
             return response()->json([
                 "message" => "Leave type not found",
             ], 422);
         }
 
         $leave->delete();
         return response()->json([
             'message' => 'Leave item deleted successfully'
         ], 200);
    }


    

   
}
