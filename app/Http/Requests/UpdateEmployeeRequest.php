<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
      
        $rules = [];
        $action = $this->input('action');
        switch ($action) {
            case 'Employee':
                $rules = [
                    'employee_id' => 'string',
                    'employee_name' => 'string|max:255',
                    'employee_email' => 'string|max:255|email',
                    'employee_set_head' => 'numeric',
                    'employee_phone' => 'numeric',
                    'employee_address' => 'string|max:255',
                    'employee_gender' => 'string|max:255',
                    'employee_role' => 'max:255|string',
                    'employee_image' => 'string|nullable',
                    'employee_status' => 'required',
                    'department_id' => 'nullable',
                    'employee_reason_status' => 'nullable',
                    'position_id' => 'nullable',
                    'employee_start_date' => 'string|nullable',
                    'employee_reason_for_leaving' => 'string|nullable',
                    'employee_end_date' => 'string|nullable',
                    'employee_age' => 'numeric|nullable',
                    'employee_height' => 'nullable',
                    'employee_weight' => 'nullable',
                    'employee_leave_credits' => 'numeric'
                ];

            break;
            case 'Employee_update_data':
            case 'Employee_set_account':
                    $rules;
            break;
          
        break;
            default:
            break;
        }
        $rules['action'] = 'string';
        return $rules;
    }
}
