@extends('layouts.admin')

@section('content')
<div class="bg-white p-6 rounded shadow-sm">
    <h2 class="text-2xl font-bold mb-4">Location Request & Approval</h2>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b bg-gray-50">
                <th class="p-3">Karyawan</th>
                <th class="p-3">Nama Lokasi</th>
                <th class="p-3">Alamat</th>
                <th class="p-3">Koordinat (Lat, Long)</th>
                <th class="p-3">Radius</th>
                <th class="p-3">Status</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($locations as $loc)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">{{ $loc->user->name }}</td>
                <td class="p-3">{{ $loc->name }}</td>
                <td class="p-3 text-sm text-gray-600">{{ $loc->address }}</td>
                <td class="p-3 font-mono text-xs">{{ $loc->latitude }}, {{ $loc->longitude }}</td>
                <td class="p-3 text-sm font-bold">{{ $loc->radius_meters }}m</td>
                <td class="p-3">
                    <span class="px-2 py-1 text-xs font-bold rounded 
                        {{ $loc->status == 'APPROVED' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $loc->status == 'PENDING' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $loc->status == 'REJECTED' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ $loc->status }}
                    </span>
                </td>
                <td class="p-3 flex gap-1">
                    @if($loc->status == 'PENDING')
                        <form action="{{ route('admin.locations.approve', $loc->id) }}" method="POST">
                            @csrf
                            <button class="bg-green-600 text-white text-xs px-2 py-1 rounded font-bold hover:bg-green-700">Approve</button>
                        </form>
                        <form action="{{ route('admin.locations.reject', $loc->id) }}" method="POST">
                            @csrf
                            <button class="bg-red-600 text-white text-xs px-2 py-1 rounded font-bold hover:bg-red-700">Reject</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $locations->links() }}</div>
</div>
@endsection