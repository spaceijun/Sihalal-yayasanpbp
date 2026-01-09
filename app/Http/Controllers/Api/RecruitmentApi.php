<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recruitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecruitmentApi extends Controller
{
    public function index(Request $request)
    {
        // Cek role user
        $user = Auth::user();
        $allowedRoles = ['superadmin', 'koordinator'];

        if (!in_array($user->role, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        $query = Recruitment::with('koordinator');

        // Filter berdasarkan koordinator jika diperlukan
        // Jika ingin koordinator hanya melihat data mereka sendiri, uncomment baris berikut:
        // if ($user->role === 'koordinator') {
        //     $query->where('koordinator_id', $user->id);
        // }

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

        // Tentukan view berdasarkan role
        $viewPath = $user->role === 'superadmin'
            ? 'superadmin.recruitment.partials.table-body'
            : 'koordinator.recruitment.partials.table-body';

        $tableHtml = view($viewPath, [
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
            $user = Auth::user();

            // Validasi role
            if (!in_array($user->role, ['superadmin', 'koordinator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak'
                ], 403);
            }

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
