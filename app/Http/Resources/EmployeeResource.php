<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
                'id' => $this->id,
                'employee_id' => $this->employee_id,
                'employee_name' => $this->employee_name,
                'employee_email' =>  $this->employee_email,
                'employee_phone' => $this->employee_phone,
                'employee_address' => $this->employee_address,
                'employee_gender' => $this->employee_gender,
                'employee_role' => $this->employee_role,
                'employee_image' =>  $this->employee_image ? \URL::to($this->employee_image) : null,
                'employee_status' => $this->employee_status,
                'employee_sss' =>  $this->employee_sss ?? "",
                'employee_pag_ibig' =>  $this->employee_pag_ibig  ?? "",
                'employee_philhealth' =>  $this->employee_philhealth ?? "",
                'employee_provincial_address' =>  $this->employee_provincial_address ?? "" ,
                'employee_birthdate' => $this->employee_birthdate,
                'employee_date_birth' => $this->employee_date_birth,
                'employee_birth_place' => $this->employee_birth_place ?? "",
                'employee_civil_status' => $this->employee_civil_status,
                'employee_spouse' => $this->employee_spouse,
                'employee_reason_status' => $this->employee_reason_status,
                'employee_name_of_spouse' => $this->employee_name_of_spouse ?? "",
                'employee_company' => $this->employee_company ?? "",
                'employee_father' =>  $this->employee_father ?? "",
                'employee_mother' => $this->employee_mother ?? "",
                'employee_tin' =>  $this->employee_tin ?? "",
                'employee_age' =>  $this->employee_age,
                'total_leaves_have' => $this->total_leaves_have,
                'employee_height' =>  $this->employee_height,
                'employee_weight' =>  $this->employee_weight,
                'employee_reason_for_leaving' => $this->employee_reason_for_leaving,
                'department_id' => $this->department_id,
                'position_id' => $this->position_id,
                'employee_start_date' => $this->employee_start_date,
                'employee_end_date' => $this->employee_end_date,
                'created_at' => $this->created_at,
                'position' => $this->position,
                'department' => $this->department,
                'employee_dependents' => $this->employee_dependents === null ? [] : json_decode($this->employee_dependents) ,
                'employee_educational_background' => $this->employee_educational_background === null ? [] : json_decode($this->employee_educational_background),
                'employee_employment_history' => $this->employee_employment_history === null ? [] : json_decode($this->employee_employment_history),
                'employee_character_reference' => $this->employee_character_reference === null ? [] : json_decode($this->employee_character_reference),
                'employee_person_to_notify' => json_decode($this->employee_person_to_notify),
                'employee_leave_credits' => (int) $this->employee_leave_credits,
        ];
    }
}
