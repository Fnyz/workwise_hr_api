<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeaveRequest extends FormRequest
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
            "department_id" => 'nullable|numeric',
            "employee_id" => 'nullable|string',
            "leave_type_id" => 'nullable',
            "leave_start_date" => 'required',
            "leave_end_date" => 'nullable|string',
            "leave_status" => 'nullable|string',
            "leave_apply_date" => 'required|string',
            "leave_reason" => 'nullable|string',
            "leave_status_date_time" => 'nullable|date_format:Y-m-d H:i:s',
            "leave_from_time" => 'nullable|string',
            "leave_to_time" => 'nullable|string',
        ];
        
    }
}
