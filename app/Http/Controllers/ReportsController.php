<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function contracts(Request $r)
    {
        $q = DB::table('contracts')
            ->join('employees','employees.id','=','contracts.employee_id')
            ->join('projects','projects.id','=','contracts.project_id')
            ->select('contracts.*','employees.full_name','projects.name as project_name');

        if ($r->filled('status'))     $q->where('contracts.status', $r->status);
        if ($r->filled('project_id')) $q->where('contracts.project_id', $r->project_id);
        if ($r->filled('date_from'))  $q->where('contracts.start_date', '>=', $r->date_from);
        if ($r->filled('date_to'))    $q->where('contracts.end_date',   '<=', $r->date_to);

        $rows = $q->orderByDesc('contracts.id')->paginate(20)->withQueryString();
        $projects = DB::table('projects')->select('id','name')->orderBy('name')->get();

        // view: resources/views/pages/laporan.blade.php (punyamu)
        return view('pages.laporan', compact('rows','projects'));
    }

    public function exportCsv(Request $r)
    {
        $filename = 'contracts_report_'.date('Ymd_His').'.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$filename,
        ];

        $callback = function() use ($r) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Contract No','Employee','Project','Start','End','Status','Base Salary','Allowance']);

            $q = DB::table('contracts')
                ->join('employees','employees.id','=','contracts.employee_id')
                ->join('projects','projects.id','=','contracts.project_id')
                ->select('contracts.*','employees.full_name','projects.name as project_name');

            if ($r->filled('status'))     $q->where('contracts.status', $r->status);
            if ($r->filled('project_id')) $q->where('contracts.project_id', $r->project_id);
            if ($r->filled('date_from'))  $q->where('contracts.start_date', '>=', $r->date_from);
            if ($r->filled('date_to'))    $q->where('contracts.end_date',   '<=', $r->date_to);

            $q->orderByDesc('contracts.id')->chunk(500, function($rows) use ($out) {
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->id,
                        $row->contract_no,
                        $row->full_name,
                        $row->project_name,
                        $row->start_date,
                        $row->end_date,
                        $row->status,
                        $row->base_salary,
                        $row->allowance,
                    ]);
                }
            });

            fclose($out);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
