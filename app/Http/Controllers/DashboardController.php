<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $in7   = $today->copy()->addDays(7);
        $in30  = $today->copy()->addDays(30);

        /**
         * EMPLOYEES PER PROJECT
         * Prefer: employment_assignments (employee_id, project_id, start_date, end_date)
         * Fallback: employees.project_id (kalau ada)
         */
        if (Schema::hasTable('employment_assignments') &&
            Schema::hasColumn('employment_assignments', 'project_id') &&
            Schema::hasColumn('employment_assignments', 'employee_id')) {

            // Hitung karyawan AKTIF per project
            $employeesCounts = DB::table('employment_assignments as ea')
                ->select('ea.project_id', DB::raw('COUNT(DISTINCT ea.employee_id) AS employees_count'))
                // aktif pada hari ini: start_date <= today dan (end_date null atau end_date >= today)
                ->when(Schema::hasColumn('employment_assignments','start_date'), function ($q) use ($today) {
                    $q->whereDate('ea.start_date','<=',$today);
                })
                ->when(Schema::hasColumn('employment_assignments','end_date'), function ($q) use ($today) {
                    $q->where(function ($w) use ($today) {
                        $w->whereNull('ea.end_date')
                          ->orWhereDate('ea.end_date','>=',$today);
                    });
                })
                ->groupBy('ea.project_id');

        } elseif (Schema::hasTable('employees') && Schema::hasColumn('employees','project_id')) {

            // Skema lama: langsung dari kolom project_id di employees
            $employeesCounts = DB::table('employees')
                ->select('project_id', DB::raw('COUNT(*) AS employees_count'))
                ->groupBy('project_id');

        } else {
            // Subquery kosong yang valid agar LEFT JOIN tidak error
            $employeesCounts = DB::table('projects')
                ->selectRaw('id as project_id, 0 as employees_count')
                ->whereRaw('1=0');
        }

        /**
         * KONTRAK AKAN BERAKHIR <= 7 / <= 30 HARI
         */
        $ending7 = DB::table('contracts')
            ->select('project_id', DB::raw('COUNT(*) AS ending_7'))
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $in7)
            ->groupBy('project_id');

        $ending30 = DB::table('contracts')
            ->select('project_id', DB::raw('COUNT(*) AS ending_30'))
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $in30)
            ->groupBy('project_id');

        /**
         * PROJECTS + agregat + koordinat
         */
        $projects = DB::table('projects as p')
            ->whereNotNull('p.latitude')
            ->whereNotNull('p.longitude')
            ->leftJoinSub($employeesCounts, 'ec', 'ec.project_id', '=', 'p.id')
            ->leftJoinSub($ending7,       'e7', 'e7.project_id', '=', 'p.id')
            ->leftJoinSub($ending30,      'e30','e30.project_id','=', 'p.id')
            ->select([
                'p.id','p.name','p.latitude','p.longitude',
                DB::raw('COALESCE(ec.employees_count,0) AS employees_count'),
                DB::raw('COALESCE(e7.ending_7,0)       AS ending_7'),
                DB::raw('COALESCE(e30.ending_30,0)     AS ending_30'),
            ])
            ->get();

        $projectPoints = $projects->map(function ($r) {
            return [
                'id'        => $r->id,
                'name'      => $r->name,
                'lat'       => (float)$r->latitude,
                'lng'       => (float)$r->longitude,
                'employees' => (int)$r->employees_count,
                'ending7'   => (int)$r->ending_7,
                'ending30'  => (int)$r->ending_30,
            ];
        });

       $stats = [
    'projects'         => (int) DB::table('projects')->count(),
    'employees'        => (int) DB::table('employees')->count(),
    'contracts_signed' => (int) DB::table('contracts')->where('status', 'SIGNED')->count(),
    'contracts_h7'     => (int) DB::table('contracts')
                                ->whereBetween('end_date', [$today->toDateString(), $in7->toDateString()])
                                ->count(),
    'contracts_h30'    => (int) DB::table('contracts')
                                ->whereBetween('end_date', [$today->toDateString(), $in30->toDateString()])
                                ->count(),
];

// kirim ke view
return view('dashboard', [
    'stats'             => $stats,
    'projectPointsJson' => $projectPoints->toJson(),
]);
    }




    public function masterData()        { return view('blank', ['title'=>'Master Data']); }
    public function generatePkwt()      { return redirect()->route('contracts.create'); }
    public function monitoringKontrak() { return view('blank', ['title'=>'Monitoring Kontrak']); }
    public function laporan()           { return view('blank', ['title'=>'Laporan']); }
}
