<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class ContractController extends Controller
{
    /**
     * /contracts/create (menu lama “Generate PKWT”) → arahkan ke kontrak terakhir,
     * supaya menu lama tidak error.
     */
    public function create()
    {
        $last = DB::table('contracts')->orderByDesc('id')->first();
        abort_unless($last, 404, 'Belum ada kontrak.');
        return redirect()->route('contracts.preview', $last->id);
    }

    /* ----------------------------------------------------------------------
     |  PREVIEW
     * --------------------------------------------------------------------*/

    /** Stream PDF langsung (tanpa public/storage) */
    public function previewPdf(int $id)
    {
        [$pdfAbs] = $this->ensurePdfUpToDate($id, false);

        return response()->file($pdfAbs, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /** Halaman preview yang menampilkan iframe PDF */
    public function preview(int $id)
    {
        $this->ensurePdfUpToDate($id, false);

        $pdfRel = "contracts/{$id}/contract.pdf";
        $pdfUrl = Storage::disk('public')->exists($pdfRel)
            ? asset('storage/'.$pdfRel)
            : null;

        return view('contracts.preview', [
            'id'     => $id,
            'pdfUrl' => $pdfUrl,
        ]);
    }

    /** Tombol “Generate Ulang” dari halaman preview */
    public function generateHtmlPackage(int $id)
    {
        $this->ensurePdfUpToDate($id, true);
        return redirect()->route('contracts.preview', $id)
            ->with('ok', 'PKWT berhasil dibuat/diupdate.');
    }

    /** Alias lama */
    public function generatePkwt(int $id) { return $this->generateHtmlPackage($id); }

    /* ----------------------------------------------------------------------
     |  LIST (menu Generate PKWT)
     * --------------------------------------------------------------------*/
    public function listForGenerate()
    {
        // id assignment terbaru per employee
        $latestEaIds = DB::table('employment_assignments')
            ->select(DB::raw('MAX(id)'))
            ->groupBy('employee_id');

        // select dinamis utk kolom unit (kalau memang ada)
        $eaSelect = ['ea.id', 'ea.employee_id', 'ea.project_id', 'ea.position', 'ea.base_salary'];
        $hasUnit  = Schema::hasColumn('employment_assignments', 'unit');
        if ($hasUnit) $eaSelect[] = 'ea.unit';

        $eaSub = DB::table('employment_assignments as ea')
            ->select($eaSelect)
            ->whereIn('ea.id', $latestEaIds);

        // kontrak terakhir per karyawan
        $lastContractPerEmp = DB::table('contracts as c')
            ->select('c.employee_id', DB::raw('MAX(c.id) as contract_id'))
            ->groupBy('c.employee_id');

        $rows = DB::table('employees as e')
            ->leftJoinSub($eaSub, 'ea', fn($j) => $j->on('ea.employee_id','=','e.id'))
            ->leftJoin('projects as p', 'p.id', '=', 'ea.project_id')
            ->leftJoinSub($lastContractPerEmp, 'lc', fn($j) => $j->on('lc.employee_id','=','e.id'))
            ->select([
                'e.id as employee_id',
                DB::raw('COALESCE(e.full_name, "") as employee_name'),
                'e.gender',                                         // <<— gender IKUT
                DB::raw('COALESCE(p.name, p.code) as project_name'),
                'ea.position',
                'ea.base_salary',
                DB::raw($hasUnit ? 'ea.unit' : '"" as unit'),       // <<— unit IKUT (kalau ada)
                DB::raw('COALESCE(lc.contract_id, 0) as contract_id'),
            ])
            ->orderByDesc('e.id')
            ->paginate(20);

        return view('contracts.generate-index', compact('rows'));
    }

    /* ----------------------------------------------------------------------
     |  Generate satu orang
     * --------------------------------------------------------------------*/
    public function generateForEmployee(Request $r)
    {
        $r->validate(['employee_id' => 'required|integer']);
        $employeeId = (int) $r->employee_id;

        // assignment terbaru
        $ea = DB::table('employment_assignments')
            ->where('employee_id', $employeeId)
            ->orderByDesc('id')->first();

        $projectId  = $ea->project_id  ?? null;
        $baseSalary = $ea->base_salary ?? 0;

        // ambil template aktif
        $templateId = DB::table('contract_templates')->where('is_active',1)->value('id');
        abort_unless($templateId, 500, 'Template PKWT belum ada/is_active=1.');

        // cari kontrak terakhir; kalau belum ada → buat draft
        $contract = DB::table('contracts')
            ->where('employee_id', $employeeId)
            ->orderByDesc('id')->first();

        if (!$contract) {
            $id = DB::table('contracts')->insertGetId([
                'employee_id' => $employeeId,
                'project_id'  => $projectId,
                'template_id' => $templateId,
                'start_date'  => now()->toDateString(),
                'end_date'    => now()->copy()->addMonths(12)->toDateString(),
                'base_salary' => $baseSalary,
                'allowance'   => 0,
                'location'    => DB::table('projects')->where('id',$projectId)->value('address'),
                'status'      => 'DRAFT',
                'contract_no' => $this->generateContractNo(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } else {
            $id = $contract->id;
        }

        // build pdf
        $this->ensurePdfUpToDate($id, true);

        return redirect()->route('contracts.preview', $id)
            ->with('ok', 'PKWT berhasil digenerate.');
    }

    /* ----------------------------------------------------------------------
     |  Bulk generate
     * --------------------------------------------------------------------*/
    public function generateBulk(Request $r)
    {
        $ids  = $r->input('employee_ids', []);
        $done = 0;

        foreach ($ids as $employeeId) {
            try {
                $this->generateForEmployee(new Request(['employee_id'=>$employeeId]));
                $done++;
            } catch (\Throwable $e) {
                // lewatkan yang gagal supaya proses tetap lanjut
            }
        }

        return back()->with('ok', "Bulk generate selesai: {$done} data.");
    }

    /* =======================================================================
     |  INTI: isi DOCX & convert ke PDF
     * =====================================================================*/
    private function ensurePdfUpToDate(int $id, bool $force = false): array
    {
        // Ambil data kontrak + employee + project + template
        $c = DB::table('contracts as c')
            ->leftJoin('employees as e', 'e.id', '=', 'c.employee_id')
            ->leftJoin('projects  as p', 'p.id', '=', 'c.project_id')
            ->leftJoin('contract_templates as t', 't.id', '=', 'c.template_id')
            ->select(
                'c.*',
                DB::raw('COALESCE(e.full_name, "") as emp_name'),
                'e.nik as emp_nik',
                'e.address as emp_address',
                'e.birth_place as emp_birth_place',
                'e.birth_date as emp_birth_date',
                'e.gender as emp_gender',                 // <<— ikut juga di sini
                'p.name as project_name',
                'p.code as project_code',
                'p.address as project_address',
                't.name as template_name'
            )
            ->where('c.id', $id)
            ->first();

        abort_unless($c, 404, 'Kontrak tidak ditemukan.');

        // Assignment terbaru (untuk POSITION, UNIT, base_salary)
        $assignment = DB::table('employment_assignments')
            ->where('employee_id', $c->employee_id)
            ->when($c->project_id, fn($q)=>$q->where('project_id', $c->project_id))
            ->orderByDesc('id')
            ->first();

        $position   = $assignment->position    ?? '';
        $unit       = $assignment->unit        ?? '';
        $baseSalary = $assignment->base_salary ?? ($c->base_salary ?? 0);

        // Data perusahaan (ENV)
        $companyName = env('COMPANY_NAME', 'Perusahaan');
        $companyAddr = env('COMPANY_ADDRESS', '-');
        $companyCity = env('COMPANY_CITY', '-');
        $hrName      = env('COMPANY_HR_NAME', 'HR');
        $hrTitle     = env('COMPANY_HR_TITLE', 'HR Manager');

        // Persentase BPJS (isi di .env, contoh "1%")
        $pctKes = env('PCT_BPJS_KESEHATAN', '1%');
        $pctJht = env('PCT_BPJS_JHT',       '2%');
        $pctJp  = env('PCT_BPJS_JP',        '1%');

        // Pastikan gender ada (fallback '-')
        $empGender = $c->emp_gender ?: (
            Schema::hasColumn('employees', 'gender')
                ? (DB::table('employees')->where('id',$c->employee_id)->value('gender') ?: '-')
                : '-'
        );

        // Payload sesuai placeholder DOCX kamu
        $payload = [
            'CONTRACT_NO'        => $c->contract_no ?: ('PKWT/'.date('Y').'/'.str_pad($c->id, 4, '0', STR_PAD_LEFT)),
            'today'              => Carbon::now()->isoFormat('D MMMM Y'),
            'CITY'               => $companyCity,

            'HR_NAME'            => $hrName,
            'HR_TITLE'           => $hrTitle,
            'COMPANY_NAME'       => $companyName,
            'COMPANY_ADDR'       => $companyAddr,

            'EMP_NAME'           => $c->emp_name ?: '-',
            'EMP_GENDER'         => $empGender ?: '-',
            'EMP_POB_DOB'        => trim(($c->emp_birth_place ?: '-').', '.($c->emp_birth_date ? Carbon::parse($c->emp_birth_date)->isoFormat('D MMMM Y') : '-')),
            'EMP_NIK'            => $c->emp_nik ?: '-',
            'EMP_ADDRESS'        => $c->emp_address ?: '-',

            'POSITION'           => $position ?: '-',
            'UNIT'               => $unit ?: '-',                 // <<— UNIT dipastikan tidak kosong
            'START_DATE'         => $c->start_date ? Carbon::parse($c->start_date)->isoFormat('D MMMM Y') : '-',
            'END_DATE'           => $c->end_date   ? Carbon::parse($c->end_date)->isoFormat('D MMMM Y')   : '-',
            'BASE_SALARY'        => number_format((float) $baseSalary, 0, ',', '.'),

            'PCT_BPJS_KESEHATAN' => $pctKes,
            'PCT_BPJS_JHT'       => $pctJht,
            'PCT_BPJS_JP'        => $pctJp,

            'SIGN_DATE'          => Carbon::now()->isoFormat('D MMMM Y'),
        ];

        // Template DOCX
        $templateRel = env('CONTRACT_TEMPLATE_DOCX', 'public/templates/pkwt.docx');
        $templateAbs = base_path($templateRel);
        abort_unless(is_file($templateAbs), 500, 'Template DOCX tidak ditemukan: '.$templateAbs);

        // Target
        $dir     = "contracts/{$c->id}";
        $docxRel = "{$dir}/contract.docx";
        $pdfRel  = "{$dir}/contract.pdf";

        $docxAbs = Storage::disk('public')->path($docxRel);
        $pdfAbs  = Storage::disk('public')->path($pdfRel);

        // Perlu rebuild?
        $needBuild = $force
            || !Storage::disk('public')->exists($pdfRel)
            || (filemtime($templateAbs) > @filemtime($pdfAbs));

        if ($needBuild) {
            if (!is_dir(dirname($docxAbs))) {
                @mkdir(dirname($docxAbs), 0777, true);
            }

            // 1) isi DOCX
            $tp = new TemplateProcessor($templateAbs);
            foreach ($payload as $k => $v) {
                $tp->setValue($k, (string) $v);
            }
            $tp->saveAs($docxAbs);

            // 2) convert ke PDF
            $soffice = env('LIBREOFFICE_BIN', 'C:\Program Files\LibreOffice\program\soffice.exe');
            $cmd = '"'.$soffice.'" --headless --nologo --norestore --convert-to pdf'
                 .' --outdir "'.dirname($pdfAbs).'" '
                 .'"'.$docxAbs.'"';

            @exec($cmd, $o, $ret);
            if ($ret !== 0 || !is_file($pdfAbs)) {
                abort(500, "Gagal convert DOCX ke PDF. Periksa LIBREOFFICE_BIN di .env.\nCommand:\n{$cmd}");
            }
        }

        $publicUrl = asset('storage/'.$pdfRel);
        return [$pdfAbs, $publicUrl];
    }

    /* ----------------------------------------------------------------------
     |  Helper nomor kontrak
     * --------------------------------------------------------------------*/
    private function generateContractNo(): string
    {
        $year = date('Y');
        $num  = (int) DB::table('contracts')->whereYear('created_at', $year)->count() + 1;
        return sprintf('PKWT/%s/%04d', $year, $num);
    }
}
