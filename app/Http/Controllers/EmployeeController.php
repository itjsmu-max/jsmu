<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $rows = DB::table('employees')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nik', 'like', "%{$q}%")
                      ->orWhere('full_name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('employees.index', compact('rows', 'q'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nik'         => 'required|string|max:30|unique:employees,nik',
            'full_name'   => 'required|string|max:191',
            'birth_place' => 'nullable|string|max:100',
            'birth_date'  => 'nullable|date',
            'email'       => 'nullable|email|max:100',
            'address'     => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:30',
        ]);

        DB::table('employees')->insert([
            'nik'         => $data['nik'],
            'full_name'   => $data['full_name'],
            'birth_place' => $data['birth_place'] ?? null,
            'birth_date'  => $data['birth_date'] ?? null,
            'email'       => $data['email'] ?? null,
            'address'     => $data['address'] ?? null,
            'phone'       => $data['phone'] ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->route('employees.index')->with('ok', 'Karyawan berhasil ditambahkan.');
    }

    public function edit($id)
{
    $row = DB::table('employees')->find($id);
    if (!$row) {
        return redirect()->route('employees.index')->with('error', 'Karyawan tidak ditemukan.');
    }

    $projects = DB::table('projects')->orderBy('name')->get(['id','name']);

    $assignment = DB::table('employment_assignments')
        ->where('employee_id', $id)
        ->orderByDesc('id')
        ->first();

    return view('employees.edit', compact('row','projects','assignment'));
}


    public function update(Request $request, $id)
{
    $data = $request->validate([
        'nik'         => "required|string|max:30|unique:employees,nik,{$id}",
        'full_name'   => 'required|string|max:191',
        'birth_place' => 'nullable|string|max:100',
        'birth_date'  => 'nullable|date',
        'email'       => 'nullable|email|max:100',
        'address'     => 'nullable|string|max:255',
        'phone'       => 'nullable|string|max:30',

        'project_id'  => 'nullable|exists:projects,id',
        'position'    => 'nullable|string|max:100',
        'base_salary' => 'nullable|numeric',
    ]);

    // update biodata
    DB::table('employees')->where('id', $id)->update([
        'nik'         => $data['nik'],
        'full_name'   => $data['full_name'],
        'birth_place' => $data['birth_place'] ?? null,
        'birth_date'  => $data['birth_date'] ?? null,
        'email'       => $data['email'] ?? null,
        'address'     => $data['address'] ?? null,
        'phone'       => $data['phone'] ?? null,
        'updated_at'  => now(),
    ]);

    // update/insert assignment
    if ($request->project_id || $request->position) {
        $assignment = DB::table('employment_assignments')
            ->where('employee_id', $id)
            ->orderByDesc('id')
            ->first();

        if ($assignment) {
            // update assignment terakhir
            DB::table('employment_assignments')
                ->where('id', $assignment->id)
                ->update([
                    'project_id'  => $request->project_id,
                    'position'    => $request->position,
                    'base_salary' => $request->base_salary,
                    'updated_at'  => now(),
                ]);
        } else {
            // insert baru
            DB::table('employment_assignments')->insert([
                'employee_id' => $id,
                'project_id'  => $request->project_id,
                'position'    => $request->position,
                'base_salary' => $request->base_salary,
                'start_date'  => now()->toDateString(),
                'end_date'    => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    return redirect()->route('employees.index')->with('ok', 'Karyawan berhasil diperbarui.');
}

    public function destroy($id)
    {
        DB::table('employees')->where('id', $id)->delete();
        return redirect()->route('employees.index')->with('ok', 'Karyawan dihapus.');
    }
}
