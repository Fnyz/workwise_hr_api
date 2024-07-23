<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use App\Http\Resources\EmployeeResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        
        $searchKeyword = request()->input('search');

        $results = Employee::select("employees.*", "p.position", "de.department")
        ->leftJoin('positions as p', 'employees.position_id', '=', 'p.id')
        ->leftJoin('departments as de', 'employees.department_id', '=', 'de.id')
        ->when($searchKeyword, function ($query) use ($searchKeyword) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->where('employees.employee_name', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('employees.employee_role', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('employees.employee_email', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('p.position', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('de.department', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('employees.employee_id', 'like', '%' . $searchKeyword . '%');
            });
        })
        ->orderBy("employees.created_at", 'desc');
    

        if ($request->has('all')) {
            return EmployeeResource::collection($results->get());
        }

        $results = $results->paginate(10)->appends(['search' => $searchKeyword]);

        return EmployeeResource::collection($results);

    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        //
   
        $datas = $request->validated();
        return $this->storeEmployee($request, $datas);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $result = Employee::select('*', DB::raw('(SELECT COUNT(*) FROM leaves AS l WHERE l.employee_id = employees.id) AS total_leaves_have'))
        ->where('id', $id)
        ->first();
        return new EmployeeResource($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, string $id)
    {
        //
    
        $data = $request->validated();
    
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
            ], 404);
        }
         
  
        switch ($request->action) {
            case 'Employee':
                return $this->updateEmployee($data, $employee);
            break;
            case 'Employee_update_data':
                return $this->update_Employee_info($request, $employee);
            break;
            case 'Employee_set_account':
                return $employee->update($request->except('action'));
            break;
            default:
                return null;
            break;
        }

        
 
    }

    public function destroy(string $id)
    {
        //
        $employee = Employee::find($id);
   
         if(!$employee){
             return response()->json([
                 "message" => "Employee type not found",
             ], 422);
         }
 
         $employee->delete();
         return response()->json([
             'message' => 'Employee item deleted successfully'
         ], 200);
    }

    
    
    private function updateEmployee($data, $employee){
        return $data;
        $base64Image = $data['employee_image'] ?? null;
        $image = $base64Image;

       
        
       if (strpos($base64Image, 'data:image/') === 0) {
       
           if (isset($employee['employee_image']) && is_string($employee['employee_image']) && \File::exists(public_path($employee['employee_image']))) {
               \File::delete(public_path($employee['employee_image']));
           }
           
          
           $imageInfo = explode(";base64,", $base64Image);
           $imgExt = str_replace('data:image/', '', $imageInfo[0]);
           $image = $imageInfo[1]; 
           $name = \Str::random(40) . '.' . $imgExt; 
         
           $dir = 'image/';
           $absolutePath = public_path($dir);
           $relativePath = $dir . $name;
           if(!\File::exists($absolutePath)){
               \File::makeDirectory($absolutePath, 0755, true); 
           }

           \file_put_contents($relativePath, base64_decode($image));

           $data["employee_image"] = $relativePath;
          
           $employee->update($data);

           return response()->json([
           'message' => 'Employee is updated successfully',
           ], 200);

         
          
       } else {


           $emp = $employee->update($data);

           return response()->json([
               'message' => 'Employee updated successfully',
               'employee' => $emp,
           ], 200);
       }


    }

    private function storeEmployee($request, $datas) {
   

   
        if ($request->has('_employeeData')) {
          
        
            // Validation rules
            $rules = [
                '_employeeData.*.employee_id' => 'required|unique:employees,employee_id',
                '_employeeData.*.department_id' => 'required',
                '_employeeData.*.position_id' => 'required',
            ];
        
            // Validation messages
            $messages = [
                '_employeeData.*.employee_id.unique' => 'employee id has already been taken',
                '_employeeData.*.employee_id.required' => 'employee id is required',
                '_employeeData.*.department_id.required' => 'department field is required',
                '_employeeData.*.position_id.required' => 'position field is required',
            ];
        
            // Validate the request data
            $validator = Validator::make($datas, $rules, $messages);
        
            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

    
         
            foreach ($datas['_employeeData'] as $data) {
            
                Employee::create([
                    'id' => IdGenerator::generate(['table' => 'employees', 'length' => 12, 'prefix' =>'ONSOURCE-']),
                    "employee_id" => $data["employee_id"],
                    'employee_name' => $data['employee_name'],
                    'employee_phone' => $data['employee_contact'],
                    'employee_address' => $data['employee_address'],
                    'employee_gender' => $data['employee_gender'],
                    'employee_role' => $data['employee_role'],
                    'employee_status' => $data['employee_status'],
                    'department_id' => $data['department_id'],
                    'position_id' => $data['position_id'],
                    'employee_start_date' => $data['employee_start_date'],
                    'employee_end_date' => $data['employee_end_date']
                ]);
            }
        
            return response()->json([
                "message" => "Employee request is successfully sent!"
            ], 200);
        }
        
 
        $base64Image = $datas['employee_image'] ?? null;
        $image = $base64Image;
        if (strpos($base64Image, 'data:image/') === 0) {
            $imageInfo = explode(";base64,", $base64Image);
            $imgExt = str_replace('data:image/', '', $imageInfo[0]);
            $image = $imageInfo[1]; // Use $imageInfo[1] to get the base64 image data
            $name = \Str::random(40) . '.' . $imgExt; 
          
            $dir = 'image/';
            $absolutePath = public_path($dir);
            $relativePath = $dir . $name;

     
            if(!\File::exists($absolutePath)){
                \File::makeDirectory($absolutePath, 0755, true); 
            }

            \file_put_contents($relativePath, base64_decode($image));

            Employee::create([
                'id' => IdGenerator::generate(['table' => 'employees', 'length' => 12, 'prefix' =>'ONSOURCE-']),
                'employee_id' => $datas['employee_id'],
                'employee_name' => $datas['employee_name'],
                'employee_email' => $datas['employee_email'],
                'employee_phone' => $datas['employee_phone'],
                'employee_address' => $datas['employee_address'],
                'employee_gender' => $datas['employee_gender'],
                'employee_role' => $datas['employee_role'],
                'employee_image' => $relativePath,
                'employee_status' => $datas['employee_status'],
                'department_id' => $datas['department_id'],
                'position_id' => $datas['position_id'],
                'employee_start_date' => $datas['employee_start_date'],
                'employee_end_date' => $datas['employee_end_date']
            ]);

            return response()->json([
            'message' => 'Employee is created successfully',
            ], 200);

     
        } else if($request->has('action')) {
            $datas["id"]= IdGenerator::generate(['table' => 'employees', 'length' => 12, 'prefix' =>'ONSOURCE-']);
            Employee::create($datas);

            return response()->json([
                "message" => "Employee account is created successfully.",
            ], 200);
           
        }else{
            Employee::create([
                'id' => IdGenerator::generate(['table' => 'employees', 'length' => 12, 'prefix' =>'ONSOURCE-']),
                'employee_id' => $datas['employee_id'],
                'employee_name' => $datas['employee_name'],
                'employee_email' => $datas['employee_email'],
                'employee_role' => $datas['employee_role'],
                'employee_image' => $datas['employee_image'],
                'employee_status' => $datas['employee_status'],
                'employee_start_date' => $datas['employee_start_date'],
            ]);

            return response()->json([
                "message" => "Your account is set successfully.",
            ], 200);
        }
    }

    private function update_Employee_info($request, $employee){
       
        $base64Image = $request->employee_image ?? null;
        $image = $base64Image;



        if (strpos($base64Image, 'data:image/') === 0) {
       
            if (isset($employee['employee_image']) && is_string($employee['employee_image']) && \File::exists(public_path($employee['employee_image']))) {
                \File::delete(public_path($employee['employee_image']));
            }
            
           
            $imageInfo = explode(";base64,", $base64Image);
            $imgExt = str_replace('data:image/', '', $imageInfo[0]);
            $image = $imageInfo[1]; 
            $name = \Str::random(40) . '.' . $imgExt; 
          
            $dir = 'image/';
            $absolutePath = public_path($dir);
            $relativePath = $dir . $name;
            if(!\File::exists($absolutePath)){
                \File::makeDirectory($absolutePath, 0755, true); 
            }
         
      
            \file_put_contents($relativePath, base64_decode($image));
        
            $request['employee_image'] = $relativePath;

            $employee->update($request->except('action', 'data', 'employee_case_emergency', 'employee_dependent', 'employee_history', 'employee_reference'));

            return response()->json([
               'message' => 'Employee updated successfully',
                'employee' => $employee,
            ], 200);
 
          
           
        } else {
          
      
            $emp = $employee->update($request->except(
                'action', 
                'data', 
                'employee_case_emergency', 
                'employee_dependent', 
                'employee_history', 
                'employee_reference'

            ));
 
            return response()->json([
                'message' => 'Employee updated successfully',
                'employee' => $emp,
            ], 200);
        }
 

      
    

    }

   

  

  

    
}
