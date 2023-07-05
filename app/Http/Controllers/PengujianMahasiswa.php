<?php

namespace App\Http\Controllers;

use App\Models\ImportLog;
use App\Helpers\NeoFeeder;
use Illuminate\Http\Request;
use App\Imports\PengujianMahasiswaImport;
class PengujianMahasiswa extends Controller
{
    public function index()
    {
        $getSemester = new NeoFeeder([
            'act' => 'GetSemester',
            'filter' => "a_periode_aktif = '1'",
            'order' => "id_semester desc"
        ]);

        return view('dashboard.pengujian-mahasiswa.index', [
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
        $pengujian = new PengujianMahasiswaImport;
        $pengujian->title('template');
        $pengujian->import($fileUpload);

        if($pengujian->failures()->isNotEmpty()) {
            return back()->with('import-error', $pengujian->failures()[0]);
        } else {
            $dataPengujian = $pengujian->toArray($fileUpload);
            // print_r($dataPengujian);
            foreach ($dataPengujian[0] as $key => $data) {
                try {
                    $recordInsertPengujianMahasiswa = [
                        'id_aktivitas' => $data['id_aktivitas'],
                        'id_kategori_kegiatan' => $data['id_kategori_kegiatan'],
                        // 'id_dosen' => $data['id_dosen'],
                        'id_dosen' => ($this->getDosen($data['nidn']))['id_dosen'],
                        'penguji_ke' => $data['penguji_ke'],

                    ];

                    $insertPengujianMahasiswa = new NeoFeeder([
                        'act' => 'InsertUjiMahasiswa',
                        'record' => $recordInsertPengujianMahasiswa
                    ]);

                    $responseInsertPengujianMahasiswa = $insertPengujianMahasiswa->getData();

                    if ($responseInsertPengujianMahasiswa['error_code'] == '0') {
                        ImportLog::create([
                            'act' => 'InsertUjiMahasiswa',
                            'status' => 'Sukses',
                            'description' => 'Pengujian Mahasiswa ' . $data['id_aktivitas'] .'NIDN: '.$data['nidn'] .' sukses diimport'
                        ]);
                    } else {
                        ImportLog::create([
                            'act' => 'InsertUjiMahasiswa',
                            'status' => 'Gagal',
                            'description' => 'Pengujian Mahasiswa ' . $data['id_aktivitas'] .'NIDN: '.$data['nidn'] .' gagal diimport. ' . $responseInsertPengujianMahasiswa['error_desc']
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
