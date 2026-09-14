<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class LocationManagementController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = EmployeeLocation::with('user');

        if ($status) {
            $query->where('status', $status);
        }

        $locations = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.locations.index', compact('locations', 'status'));
    }

    public function approve($id)
    {
        $location = EmployeeLocation::findOrFail($id);
        
        $location->update([
            'status'      => 'APPROVED',
            'approved_by' => Auth::id(), // Menggunakan Auth::id() yang lebih stabil
            'approved_at' => now(),
        ]);

        // Gunakan optional chaining (?->) untuk mencegah error jika relasi user bernilai null
        $userName = $location->user?->name ?? 'Pengguna';

        return back()->with('success', "Lokasi {$location->name} milik {$userName} telah disetujui.");
    }

    public function reject($id)
    {
        $location = EmployeeLocation::findOrFail($id);
        $location->update(['status' => 'REJECTED']);

        $userName = $location->user?->name ?? 'Pengguna';

        return back()->with('success', "Lokasi {$location->name} milik {$userName} ditolak.");
    }
}