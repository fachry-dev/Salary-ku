<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $employee_id
 * @property string $month
 * @property int $year
 * @property int $working_days
 * @property int $present_days
 * @property int $absent_days
 * @property numeric $base_salary
 * @property numeric $deductions
 * @property numeric $bonus
 * @property numeric $net_salary
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $payment_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Employee $employee
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereAbsentDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereBaseSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereDeductions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereNetSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll wherePresentDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereWorkingDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereYear($value)
 * @mixin \Eloquent
 */
class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'working_days',
        'present_days',
        'absent_days',
        'base_salary',
        'deductions',
        'bonus',
        'net_salary',
        'status',
        'payment_date',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'base_salary' => 'decimal:2',
        'deductions' => 'decimal:2',
        'bonus' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function calculateNetSalary()
    {
        return $this->base_salary - $this->deductions + $this->bonus;
    }
}