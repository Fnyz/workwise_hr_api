<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    
     
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "department_id" => 'numeric|required',
            "employee_id" => 'string|required',
            "leave_type_id" => 'required|unique:leaves,leave_type_id,NULL,id,employee_id,' . request('employee_id') . ',leave_start_date,' . request('leave_start_date'),
            "leave_start_date" => 'string|required|unique:leaves,leave_start_date,NULL,id,employee_id,' . request('employee_id') . ',leave_end_date,' . request('leave_end_date'),
            "leave_end_date" => 'string|required',
            "leave_apply_date" => 'string|required',
            "leave_reason" => 'string|required',
            "leave_status" => 'string|required',
            "employee_approval_role" => 'string|nullable',
            "employee_approval_id" => 'string|nullable',
            "leave_from_time" => 'nullable',
            "leave_to_time" => 'nullable'
        ];
        
        
    }
}
