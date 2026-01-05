<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recruitment;
use Illuminate\Http\Request;

class RecruitmentApi extends Controller
{
    public function index(Request $request)
    {
        $query = Recruitment::with('koordinator');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $recruitments = $query->paginate(10);

        $tableHtml = view('superadmin.recruitment.partials.table-body', [
            'recruitments' => $recruitments
        ])->render();

        $paginationHtml = view('layouts.pagination', [
            'paginator' => $recruitments
        ])->render();

        return response()->json([
            'success' => true,
            'table' => $tableHtml,
            'pagination' => $paginationHtml
        ]);
    }

    public function destroy($id)
    {
        try {
            $recruitment = Recruitment::findOrFail($id);
            $recruitment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data recruitment berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
