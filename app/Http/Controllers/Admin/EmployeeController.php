<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user')->orderBy('name', 'asc');
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_id', 'like', '%' . $request->search . '%');
        }
        $employees = $query->paginate(10);
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'employee_id' => ['required', 'string', 'max:50', 'unique:employees'],
            'position' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'join_date' => ['required', 'date'],
            'base_salary' => ['required', 'numeric', 'min:0'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
        ]);

        $user->employee()->create([
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'department' => $request->department,
            'address' => $request->address,
            'phone' => $request->phone,
            'join_date' => $request->join_date,
            'base_salary' => $request->base_salary,
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Employee added successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load('user');
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $employee->load('user');
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employee->user_id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'employee_id' => ['required', 'string', 'max:50', 'unique:employees,employee_id,' . $employee->id],
            'position' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'join_date' => ['required', 'date'],
            'base_salary' => ['required', 'numeric', 'min:0'],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        
        $employee->user->update($userData);

        $employee->update([
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'department' => $request->department,
            'address' => $request->address,
            'phone' => $request->phone,
            'join_date' => $request->join_date,
            'base_salary' => $request->base_salary,
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Employee data updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        // Be careful: This will delete the user as well due to onDelete('cascade') in the employee migration
        // If you don't want the user to be deleted, change the relationship or delete the employee manually.
        $employee->user->delete(); // Or $employee->delete(); if you want to keep the user.
        // If only $employee->delete(), make sure the user is handled (e.g., disabled)
        
        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully.');
    }
}