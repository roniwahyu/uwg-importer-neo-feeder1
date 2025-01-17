<?php

namespace App\Http\Controllers;

use App\Models\ImportLog;
use App\Helpers\NeoFeeder;
use Illuminate\Http\Request;
use App\Imports\BimbinganMahasiswaImport;
class BimbinganMahasiswa extends Controller
{
    public function index()
    {
        $getSemester = new NeoFeeder([
            'act' => 'GetSemester',
            'filter' => "a_periode_aktif = '1'",
            'order' => "id_semester desc"
        ]);

        return view('dashboard.bimbingan-mahasiswa.index', [
            'semester' => $getSemester->getData()
        ]);
    }
    public function getDosen($nidn){
        // $nidn="0725108202";
        $getDosen = new NeoFeeder([
            'act' => 'GetListDosen',
            'filter' => "nidn = '".$nidn."'",
            'order' => ""
        ]);
        // return view('dashboard.bimbingan-mahasiswa.print', [
            // 'semester' => ($getDosen->getData())['data'][0] //getDataDosen
            // ]);
        return ($getDosen->getData())['data'][0]; //getDataDosen
    }
    public function store(Request $request)
    {
        $request->validate([
            'semester' => 'required',
            'file' => 'required|mimes:xls,xlsx'
        ]);

        $fileUpload = $request->file('file');
        $bimbingan = new BimbinganMahasiswaImport;
        $bimbingan->title('template');
        $bimbingan->import($fileUpload);

        if($bimbingan->failures()->isNotEmpty()) {
            return back()->with('import-error', $bimbingan->failures()[0]);
        } else {
            $dataBimbingan = $bimbingan->toArray($fileUpload);
            // print_r($dataBimbingan);
            // return $dataBimbingan;
            foreach ($dataBimbingan[0] as $key => $data) {
                try {
                    $recordInsertBimbinganMahasiswa = [
                        'id_aktivitas' => $data['id_aktivitas'],
                        'id_kategori_kegiatan' => $data['id_kategori_kegiatan'],
                        'id_dosen' => ($this->getDosen($data['nidn']))['id_dosen'],
                        'pembimbing_ke' => $data['pembimbing_ke'],

                    ];

                    $insertBimbinganMahasiswa = new NeoFeeder([
                        'act' => 'InsertBimbingMahasiswa',
                        'record' => $recordInsertBimbinganMahasiswa
                    ]);

                    $responseInsertBimbinganMahasiswa = $insertBimbinganMahasiswa->getData();

                    if ($responseInsertBimbinganMahasiswa['error_code'] == '0') {
                        ImportLog::create([
                            'act' => 'InsertBimbingMahasiswa',
                            'status' => 'Sukses',
                            'description' => 'Bimbingan Mahasiswa NIDN:'.$data['nidn'].' IDAKTV: ' . $data['id_aktivitas'] . ' sukses diimport'
                        ]);
                    } else {
                        ImportLog::create([
                            'act' => 'InsertBimbingMahasiswa',
                            'status' => 'Gagal',
                            'description' => 'Bimbingan Mahasiswa NIDN:'.$data['nidn'].' IDAKTV: ' . $data['id_aktivitas'] . ' gagal diimport. ' . $responseInsertBimbinganMahasiswa['error_desc']
                        ]);
                    }

                } catch (\Throwable $th) {
                    //throw $th;
                }
            } 

            return redirect()->back()->with('success', 'Sukses import file. Riwayat import dapat dilihat pada menu Log Import');
        }
    }//
}
