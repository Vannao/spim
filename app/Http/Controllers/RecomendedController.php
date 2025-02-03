<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\RecomendedTable;
use Illuminate\Support\Facades\DB;
use App\Models\Recomended;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Enums\Status;
use App\Models\Audit;

class RecomendedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RecomendedTable $dataTable, $auditId)
    {
        return $dataTable->render('Dashboard.Rekomendasi.rekomendasi', ['auditId' => $auditId]);
    }

    /**
     * Show the form for creating a new resource.
     */
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
        ], [
            'title.required' => 'Rekomendasi harus diisi!',
            'status.required' => 'Status harus diisi!',
        ]);

        DB::beginTransaction();
        try {

            Recomended::create([
                'audit_id' => $auditId,
                'title' => $request->title,
                'status' => $request->status,
            ]);
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
