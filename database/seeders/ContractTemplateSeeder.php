<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ContractTemplateSeeder extends Seeder {
  public function run(): void {
    DB::table('contract_templates')->updateOrInsert(
      ['name'=>'PKWT Default'],
      ['body_html'=><<<HTML
<div style="font-family:'Times New Roman';font-size:12pt;line-height:1.5">
  <h3 style="text-align:center">PERJANJIAN KERJA WAKTU TERTENTU (PKWT)</h3>
  <p>Pihak Pertama: <b><span style="color:red">{{ perusahaan_nama }}</span></b></p>
  <p>Pihak Kedua: <b><span style="color:red">{{ employee.full_name }}</span></b> (NIK: <span style="color:red">{{ employee.nik }}</span>)</p>
  <p>Penempatan: <b><span style="color:red">{{ project.name }}</span></b> — <span style="color:red">{{ project.address }}</span></p>
  <p>Masa kerja: <span style="color:red">{{ contract.start_date }}</span> s.d. <span style="color:red">{{ contract.end_date }}</span></p>
  <p>Gaji Pokok: Rp <span style="color:red">{{ contract.base_salary }}</span> &nbsp; Tunjangan: Rp <span style="color:red">{{ contract.allowance }}</span></p>
  <p>Lokasi kerja: <span style="color:red">{{ contract.location }}</span> | Nomor: <span style="color:red">{{ contract.contract_no }}</span></p>
  <hr/>
  <div style="display:flex;justify-content:space-between;margin-top:36px">
    <div>
      <p>Pekerja</p>
      <img src="{{ signature.employee }}" style="height:80px" />
      <p><b>{{ employee.full_name }}</b></p>
    </div>
    <div>
      <p>Perusahaan</p>
      <img src="{{ signature.hr }}" style="height:80px" />
      <p><b>{{ perusahaan_perwakilan }}</b></p>
    </div>
  </div>
</div>
HTML
      ]
    );
  }
}