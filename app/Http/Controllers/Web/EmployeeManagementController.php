<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeManagementController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = User::where('role', 'EMPLOYEE');

        if ($status) {
            $query->where('status', $status);
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.employees.index', compact('employees', 'status'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:ACTIVE,REJECTED,SUSPENDED,INACTIVE'
        ]);

        $employee = User::where('role', 'EMPLOYEE')->findOrFail($id);
        $employee->update(['status' => $request->status]);

        return back()->with('success', "Status karyawan {$employee->name} berhasil diperbarui menjadi {$request->status}.");
    }
}
