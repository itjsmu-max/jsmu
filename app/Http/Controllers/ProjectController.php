<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * LIST + Search + Pagination
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $builder = DB::table('projects');

        if ($q !== '') {
            $builder->where(function ($w) use ($q) {
                $w->where('code', 'like', "%{$q}%")
                  ->orWhere('name', 'like', "%{$q}%")
                  ->orWhere('location', 'like', "%{$q}%")
                  ->orWhere('address', 'like', "%{$q}%");
            });
        }

        $projects = $builder->orderByDesc('id')
                            ->paginate(15)
                            ->withQueryString(); // supaya query q tetap ada saat pindah halaman
  // kirim keduanya: $projects dan $rows
    $rows = $projects;
        // kirim $q ke view -> fix "Undefined variable $q"
         return view('projects.index', compact('projects', 'rows', 'q'));
    }

    /**
     * CREATE FORM
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'code'      => 'required|max:50|unique:projects,code',
            'name'      => 'required|max:191',
            'location'  => 'nullable|max:191',
            'address'   => 'nullable|max:255',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        DB::table('projects')->insert([
            'code'       => $request->code,
            'name'       => $request->name,
            'location'   => $request->location,
            'address'    => $request->address,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('projects.index')->with('ok', 'Project berhasil ditambahkan.');
    }

    /**
     * EDIT FORM
     */
    public function edit($id)
    {
        $project = DB::table('projects')->find($id);
        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project tidak ditemukan.');
        }
        return view('projects.edit', compact('project'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'code'      => 'required|max:50|unique:projects,code,'.$id,
            'name'      => 'required|max:191',
            'location'  => 'nullable|max:191',
            'address'   => 'nullable|max:255',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        DB::table('projects')->where('id', $id)->update([
            'code'       => $request->code,
            'name'       => $request->name,
            'location'   => $request->location,
            'address'    => $request->address,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'updated_at' => now(),
        ]);

        return redirect()->route('projects.index')->with('ok', 'Project berhasil diperbarui.');
    }

    /**
     * DESTROY
     */
    public function destroy($id)
    {
        DB::table('projects')->where('id', $id)->delete();
        return redirect()->route('projects.index')->with('ok', 'Project berhasil dihapus.');
    }
}
