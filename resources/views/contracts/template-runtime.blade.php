{{-- resources/views/contracts/template.blade.php --}}
{{-- Variabel yang dipakai: 
     $template->body_html  (HTML dengan placeholder Blade)
     $employee (object/array)
     $project  (object/array)
     $contract (object/array)
     $sign     (object/array)
     $hr_name  (string)
--}}

@php
    // pastikan default agar tidak meledak
    $employee = (object) array_merge([
        'name'    => '',
        'gender'  => '-',     // <<— default
        'unit'    => '-',     // <<— default
        'nik'     => '',
        'address' => '',
        'birth_place' => '',
        'birth_date'  => '',
    ], (array) ($employee ?? []));

    $project  = (object) array_merge([
        'name'    => '',
        'code'    => '',
        'address' => '',
    ], (array) ($project ?? []));

    $contract = (object) array_merge([
        'contract_no' => '',
        'start_date'  => '',
        'end_date'    => '',
        'base_salary' => 0,
    ], (array) ($contract ?? []));

    $sign     = (object) array_merge([
        'date' => now()->isoFormat('D MMMM Y'),
    ], (array) ($sign ?? []));

    $hr_name  = $hr_name ?? 'Human Resources';
@endphp

{!! \Illuminate\Support\Facades\Blade::render($template->body_html, [
    'employee' => $employee,
    'project'  => $project,
    'contract' => $contract,
    'sign'     => $sign,
    'hr_name'  => $hr_name,
]) !!}
