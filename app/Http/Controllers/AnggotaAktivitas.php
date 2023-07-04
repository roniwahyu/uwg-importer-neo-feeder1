<?php

namespace App\Http\Controllers;

use App\Models\ImportLog;
use App\Helpers\NeoFeeder;
use Illuminate\Http\Request;
use App\Imports\AnggotaAktivitasImport;

class AnggotaAktivitas extends Controller
{
    public function index()
    {
        $getSemester = new NeoFeeder([
            'act' => 'GetSemester',
            'filter' => "a_periode_aktif = '1'",
            'order' => "id_semester desc"
        ]);

        return view('dashboard.anggota-aktivitas.index', [
            'semester' => $getSemester->getData()
        ]);
    }
    public function getMahasiswa($nim){
        // $nim="0725108202";
        $getMahasiswa = new NeoFeeder([
            'act' => 'GetListMahasiswa',
            'filter' => "nim = '".$nim."'",
            'order' => ""
        ]);
        // return view('dashboard.bimbingan-mahasiswa.print', [
            // 'semester' => ($getDosen->getData())['data'][0] //getDataDosen
            // ]);
        return ($getMahasiswa->getData())['data'][0]; //getDataDosen
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester' => 'required',
            'file' => 'required|mimes:xls,xlsx'
        ]);

        $fileUpload = $request->file('file');
        $aktivitas = new AnggotaAktivitasImport;
        $aktivitas->import($fileUpload);

        if($aktivitas->failures()->isNotEmpty()) {
            return back()->with('import-error', $aktivitas->failures()[0]);
        } else {
            $dataAktivitas = $aktivitas->toArray($fileUpload);

            foreach ($dataAktivitas[0] as $key => $data) {
                try {
                    $recordInsertAnggotaAktivitasKampusMahasiswa = [
                        'id_aktivitas' => $data['id_aktivitas'],
                        // 'id_registrasi_mahasiswa' => $data['id_registrasi_mahasiswa'],
                        // 'id_dosen' => ($this->getDosen($data['nidn']))['id_dosen'],
                        'id_registrasi_mahasiswa' => ($this->getMahasiswa($data['nim']))['id_registrasi_mahasiswa'],
                        'jenis_peran' => $data['jenis_peran'],
                    
                    ];

                    $insertAktivitasMahasiswa = new NeoFeeder([
                        'act' => 'InsertAnggotaAktivitasKampusMahasiswa',
                        'record' => $recordInsertAnggotaAktivitasKampusMahasiswa
                    ]);

                    $responseInsertAnggotaAktivitasKampusMahasiswa = $insertAktivitasMahasiswa->getData();

                    if ($responseInsertAnggotaAktivitasKampusMahasiswa['error_code'] == '0') {
                        ImportLog::create([
                            'act' => 'InsertAnggotaAktivitasKampusMahasiswa',
                            'status' => 'Sukses',
                            'description' => 'Import Anggota Aktivitas Mahasiswa <br> IDAKTV:' . $data['id_aktivitas'] . 'dengan IDREGMHS:'.$data['id_registrasi_mahasiswa'].' sukses diimport'
                        ]);
                    } else {
                        ImportLog::create([
                            'act' => 'InsertAnggotaAktivitasKampusMahasiswa',
                            'status' => 'Gagal',
                            'description' => 'Import Anggota Aktivitas Mahasiswa <br> IDAKTV:' . $data['id_aktivitas'] . 'dengan IDREGMHS:'.$data['id_registrasi_mahasiswa'].' gagal diimport. ' . $responseInsertAnggotaAktivitasKampusMahasiswa['error_desc']
                        ]);
                    }

                } catch (\Throwable $th) {
                    //throw $th;
                }
            }

            return redirect()->back()->with('success', 'Sukses import file. Riwayat import dapat dilihat pada menu Log Import');
        }
    }
}
