<?php

namespace App\Http\Controllers;

use App\Models\ImportLog;
use App\Helpers\NeoFeeder;
use Illuminate\Http\Request;
use App\Imports\KampusMerdekaImport;
class KampusMerdeka extends Controller
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
    public function getAnggotaMhs($nim,$idaktivitas){
        // $nim="0725108202";
        $getAnggotaMhs = new NeoFeeder([
            'act' => 'GetListAnggotaAktivitasMahasiswa',
            'filter' => "nim = '".$nim."' and id_aktivitas='".$idaktivitas."'",
            'order' => ""
        ]);
        // return view('dashboard.bimbingan-mahasiswa.print', [
            // 'semester' => ($getAnggotaMhs->getData())['data'][0] //getAnggotaDataMhs
            // ]);
        return ($getAnggotaMhs->getData())['data'][0]; //getDataDosen
    }
    public function store(Request $request)
    {
        $request->validate([
            'semester' => 'required',
            'file' => 'required|mimes:xls,xlsx'
        ]);

        $fileUpload = $request->file('file');
        $mbkm = new KampusMerdekaImport;
        $mbkm->title('template');
        $mbkm->import($fileUpload);

        if($mbkm->failures()->isNotEmpty()) {
            return back()->with('import-error', $mbkm->failures()[0]);
        } else {
            $dataMbkm = $mbkm->toArray($fileUpload);
            // print_r($dataMbkm);
            // return $dataMbkm;
            foreach ($dataMbkm[0] as $key => $data) {
                try {
                    $recordInsertKampusMerdeka = [
                        'id_matkul' => $data['id_matkul'],
                        'id_aktivitas' => $data['id_aktivitas'],
                        'id_anggota' => ($this->getAnggotaMhs($data['nim'],$data['id_aktivitas']))['id_anggota'],
                        'sks_mata_kuliah' => $data['sks_mata_kuliah'],
                        'nilai_angka' => $data['nilai_angka'],
                        'nilai_index' => $data['nilai_index'],
                        'nilai_huruf' => $data['nilai_huruf'],

                    ];

                    $insertKampusMerdeka = new NeoFeeder([
                        'act' => 'InsertKonversiKampusMerdeka',
                        'record' => $recordInsertKampusMerdeka
                    ]);

                    $responseInsertKampusMerdeka = $insertKampusMerdeka->getData();

                    if ($responseInsertKampusMerdeka['error_code'] == '0') {
                        ImportLog::create([
                            'act' => 'InsertKonversiKampusMerdeka',
                            'status' => 'Sukses',
                            'description' => 'Konversi Kampus Merdeka NIM:'.$data['nim'].' IDAKTV: ' . $data['id_aktivitas'] .' IDMK:'.$data['id_matkul']. ' sukses diimport'
                        ]);
                    } else {
                        ImportLog::create([
                            'act' => 'InsertKonversiKampusMerdeka',
                            'status' => 'Gagal',
                            'description' => 'Konversi Kampus Merdeka NIM:'.$data['nim'].' IDAKTV: ' . $data['id_aktivitas'] .' IDMK:'.$data['id_matkul']. ' gagal diimport. ' . $responseInsertKampusMerdeka['error_desc']
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
