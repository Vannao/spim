<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\LaporanHasilAuditTable;
use App\Http\Requests\AuditRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class LaporanHasilAuditController extends Controller
{

    public function __construct() {}

    /**
     * Display a listing of the resource.
     */
    public function index(LaporanHasilAuditTable $dataTable)
    {
        return $dataTable->render('Dashboard.laporan-hasil-audit');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Dashboard.form-hasil-audit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nomorLaporan' => 'required|unique:audits,code',
            'tanggal' => 'required|date',
            'divisi' => 'required|string',
            'judul' => 'required|string',
            'surat_tugas' => 'required|file|mimes:pdf',
            'nota_dinas' => 'required|file|mimes:pdf',
            'bentuk_kegiatan' => 'required|string',
            'berita_acara_exit_meeting' => 'required|file|mimes:pdf',
            'pka' => 'required|file|mimes:pdf',
            'laporan_dan_lampiran' => 'required|file|mimes:pdf',
            'anggota' => 'nullable|array',
            'anggota.*.anggota' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Pastikan folder penyimpanan ada
            Storage::makeDirectory('public/audit/uploads');

            // Simpan anggota jika ada
            $member = array_filter(array_column($request->anggota ?? [], 'anggota'));

            // Simpan file dengan path yang lebih terstruktur
            $filePaths = [
                'file_surat_tugas' => $request->file('surat_tugas')->store('audit/uploads', 'public'),
                'file_nota_dinas' => $request->file('nota_dinas')->store('audit/uploads', 'public'),
                'berita_acara_exit_meeting' => $request->file('berita_acara_exit_meeting')->store('audit/uploads', 'public'),
                'pka' => $request->file('pka')->store('audit/uploads', 'public'),
                'laporan_dan_lampiran' => $request->file('laporan_dan_lampiran')->store('audit/uploads', 'public'),
            ];

            // Simpan ke database
            Audit::create([
                'code' => $request->nomorLaporan,
                'date' => $request->tanggal,
                'divisi' => $request->divisi,
                'title' => $request->judul,
                'activity' => $request->bentuk_kegiatan,
                'member' => $member ? json_encode($member) : null,
            ] + $filePaths);

            DB::commit();
            return redirect()->route('audit.index')->with('success', 'Data berhasil ditambahkan!');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Audit::find($id);
        return response()->json(['data' => $data], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Audit::find($id);
        return view('Dashboard.form-hasil-audit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nomorLaporan' => 'required|unique:audits,code,' . $id,
            'tanggal' => 'required|date',
            'divisi' => 'required|string',
            'judul' => 'required|string',
            'surat_tugas' => 'nullable|file|mimes:pdf',
            'nota_dinas' => 'nullable|file|mimes:pdf',
            'bentuk_kegiatan' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $dataOld = Audit::find($id);
            $member = [];
            if ($request->anggota) {
                foreach ($request->anggota as $key => $value) {
                    if ($value['anggota']) $member[] = $value['anggota'];
                }
            }

            $dataUpdate = [
                'code' => $request->nomorLaporan,
                'date' => $request->tanggal,
                'divisi' => $request->divisi,
                'title' => $request->judul,
                'activity' => $request->bentuk_kegiatan,
                'member' => !$member ? NULL : json_encode($member),
            ];

            if ($request->file('surat_tugas')) {
                Storage::delete($dataOld->file_surat_tugas);
                $dataUpdate['file_surat_tugas'] = $request->file('surat_tugas')->store('uploads', 'public');
            }

            if ($request->file('nota_dinas')) {
                Storage::delete($dataOld->file_nota_dinas);
                $dataUpdate['file_nota_dinas'] = $request->file('nota_dinas')->store('uploads', 'public');
            }

            Audit::where('id', $id)->update($dataUpdate);
            DB::commit();
            return redirect()->route('audit.index')->with('success', 'Successfully update Data!');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $data = Audit::find($request->dataId);
        try {
            Storage::delete($data->file_surat_tugas);
            Storage::delete($data->file_nota_dinas);
            $data->delete();

            return response()->json(['status' => true, 'message' => 'delete data successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'delete data failed'], 400);
        }
    }
}
