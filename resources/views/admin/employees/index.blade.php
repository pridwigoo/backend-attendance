@extends('layouts.admin')

@section('content')
<div class="bg-white p-6 rounded shadow-sm">
    <h2 class="text-2xl font-bold mb-4">Employee Management & Approval</h2>
    
    <div class="flex gap-2 mb-6">
        <a href="{{ route('admin.employees.index') }}" class="px-3 py-1 bg-gray-200 rounded text-sm font-semibold">Semua</a>
        <a href="{{ route('admin.employees.index', ['status' => 'PENDING']) }}" class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded text-sm font-semibold">Pending</a>
        <a href="{{ route('admin.employees.index', ['status' => 'ACTIVE']) }}" class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm font-semibold">Active</a>
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b bg-gray-50">
                <th class="p-3">ID</th>
                <th class="p-3">Nama</th>
                <th class="p-3">Email</th>
                <th class="p-3">No. Telp</th>
                <th class="p-3">Status</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $emp)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-mono text-sm">{{ $emp->employee_id }}</td>
                <td class="p-3 font-semibold">{{ $emp->name }}</td>
                <td class="p-3">{{ $emp->email }}</td>
                <td class="p-3">{{ $emp->phone ?? '-' }}</td>
                <td class="p-3">
                    <span class="px-2 py-1 text-xs font-bold rounded 
                        {{ $emp->status == 'ACTIVE' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $emp->status == 'PENDING' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $emp->status == 'REJECTED' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $emp->status == 'SUSPENDED' ? 'bg-gray-100 text-gray-800' : '' }}">
                        {{ $emp->status }}
                    </span>
                </td>
                <td class="p-3 flex gap-1">
                    @if($emp->status == 'PENDING')
                        <form action="{{ route('admin.employees.updateStatus', $emp->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="ACTIVE">
                            <button class="bg-green-600 text-white text-xs px-2 py-1 rounded font-bold hover:bg-green-700">Approve</button>
                        </form>
                        <form action="{{ route('admin.employees.updateStatus', $emp->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="REJECTED">
                            <button class="bg-red-600 text-white text-xs px-2 py-1 rounded font-bold hover:bg-red-700">Reject</button>
                        </form>
                    @elseif($emp->status == 'ACTIVE')
                        <form action="{{ route('admin.employees.updateStatus', $emp->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="SUSPENDED">
                            <button class="bg-gray-600 text-white text-xs px-2 py-1 rounded font-bold hover:bg-gray-700">Suspend</button>
                        </form>
                    @else
                        <form action="{{ route('admin.employees.updateStatus', $emp->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="ACTIVE">
                            <button class="bg-blue-600 text-white text-xs px-2 py-1 rounded font-bold hover:bg-blue-700">Activate</button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-4 text-center text-gray-500">Tidak ada data karyawan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $employees->links() }}</div>
</div>
@endsection