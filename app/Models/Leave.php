<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        "department_id",
        "employee_id",
        "leave_type_id",
        "leave_start_date",
        "leave_end_date",
        "leave_status",
        "leave_apply_date",
        "leave_reason",
        "leave_signature_image",
        "leave_status",
        "leave_status_date_time",
        "employee_approval_role",
        "employee_approval_id",
        "leave_from_time",
        "leave_to_time"
    ];

    protected $dates = ['leave_status_date_time'];

   

    /**
     * Get the leavetype that owns the Leave
     *
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Leave_type::class, 'leave_type_id');
    }


    /**
     * Get the employee that owns the Leave
     *
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }


    /**
     * Get the employee that owns the Leave
     *
     * @return BelongsTo
     */
    public function departmentHead(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_approval_id');
    }


    public function scopeJoinEmployee($query)
    {
        return $query->join('employees as emp', 'emp.id', '=', 'l.employee_id');
    }

    // Scope for joining with leave_types table
    public function scopeJoinLeaveType($query)
    {
        return $query->join('leave_types as lt', 'lt.id', '=', 'l.leave_type_id');
    }


    // Scope for joining with departments table
    public function scopeJoinDepartment($query)
    {
        return $query->join('departments as de', 'de.id', '=', 'l.department_id');
    }


}
