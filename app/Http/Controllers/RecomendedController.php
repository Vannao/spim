<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Audit;
use App\Models\Recomended;
use App\Models\Recomendeds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\RecomendedTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class RecomendedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RecomendedTable $dataTable, $auditId)
    {
        return $dataTable->render('Dashboard.Rekomendasi.rekomendasi', ['auditId' => $auditId]);
    }

    public function tampilTable(Request $request)
    {
        $recomendeds = Recomended::with('audit')
            ->whereIn('status', [1, 2])
            ->when($request->divisi, function ($query, $divisi) {
                return $query->whereHas('audit', function ($q) use ($divisi) {
                    $q->where('divisi', $divisi);
                });
            })
            ->paginate(5);


        return view('Tindak-Lanjut.rekomendasi', [
            'recomendeds' => $recomendeds,
        ]);
    }


    public function halamanUpdateRecomendeds($id)
    {
        $recomendeds = Recomended::findOrFail($id);
        return view('Tindak-Lanjut.ubah-status-tl', ['recomendeds' => $recomendeds]);
    }


    public function updateRecomendeds(Request $request, $id)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $recomendeds = Recomended::findOrFail($id);

        $recomendeds->update([
            'status' => $request->status,
        ]);


        return redirect()->to('tindak-lanjut')->with('success', 'Status Diubah');
    }















    public function create($auditId)
    {
        return view('Dashboard..Rekomendasi.form-rekomendasi', [
            'auditId' => $auditId,
            'audit' => Audit::where('id', $auditId)->first(),
            'statuses' => Status::asOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $auditId): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'status' => 'required|string',
            'batas_waktu' => 'required|date',
            'pic' => 'required|string',

        ], [
            'title.required' => 'Rekomendasi harus diisi!',
            'status.required' => 'Status harus diisi!',
            'batas_waktu.required' => 'Batas Waktu Tolong Diisi',
            'pic.required' => 'Pic harus diisi!',

        ]);

        DB::beginTransaction();
        try {

            Recomended::create([
                'audit_id' => $auditId,
                'title' => $request->title,
                'status' => $request->status,
                'batas_waktu' => $request->batas_waktu,
                'pic' => $request->pic

            ]);
            // dd($request->all());

            DB::commit();
            return redirect()->route('audit.rekomendasi.index', $auditId)->with('success', 'Successfully Added Data!');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $auditId, string $id)
    {
        return view('Dashboard..Rekomendasi.form-rekomendasi', [
            'data' => Recomended::find($id),
            'auditId' => $auditId,
            'audit' => Audit::where('id', $auditId)->first(),
            'statuses' => Status::asOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $auditId, string $id)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'status' => 'required|string',
        ], [
            'title.required' => 'Rekomendasi harus diisi!',
            'status.required' => 'Status harus diisi!',
        ]);

        DB::beginTransaction();
        try {
            Recomended::where('id', $id)->update([
                'audit_id' => $auditId,
                'title' => $request->title,
                'status' => $request->status,
            ]);
            DB::commit();
            return redirect()->route('audit.rekomendasi.index', $auditId)->with('success', 'Successfully update Data!');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to update Data!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $data = Recomended::find($request->dataId);
        try {
            $data->delete();

            return response()->json(['status' => true, 'message' => 'delete data successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'delete data failed'], 400);
        }
    }
}
