<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EnumeratorApi extends Controller
{
    /**
     * Display a listing of the resource via API.
     */
    public function index(Request $request)
    {
        try {
            $query = Enumerator::with('koordinator')
                ->withCount([
                    'dataLapangans as data_bulan_ini' => function ($q) {
                        $startDate = now()->day >= 25
                            ? now()->startOfDay()->day(25)
                            : now()->subMonth()->day(25)->startOfDay();

                        $endDate = $startDate->copy()->addMonth();

                        $q->whereBetween('created_at', [$startDate, $endDate]);
                    }
                ]);

            // Search filter (nama enumerator dan koordinator)
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('telephone', 'like', "%{$search}%")
                        ->orWhereHas('koordinator', function ($q2) use ($search) {
                            $q2->where('nama_lengkap', 'like', "%{$search}%");
                        });
                });
            }

            // Status filter
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            // Paginate
            $perPage = $request->get('per_page', 15);
            $enumerators = $query->latest()->paginate($perPage);

            // Hitung nomor urut
            $i = ($enumerators->currentPage() - 1) * $enumerators->perPage();

            // Render table body
            $tableHtml = view('superadmin.enumerator.partials.table-body', [
                'enumerators' => $enumerators,
                'i' => $i
            ])->render();

            // Render pagination
            $paginationHtml = view('layouts.pagination', [
                'paginator' => $enumerators
            ])->render();

            return response()->json([
                'success' => true,
                'table' => $tableHtml,
                'pagination' => $paginationHtml,
                'total' => $enumerators->total(),
                'current_page' => $enumerators->currentPage(),
                'last_page' => $enumerators->lastPage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage via API.
     */
    public function destroy($id)
    {
        try {
            $enumerator = Enumerator::findOrFail($id);
            $enumerator->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data enumerator berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateUser($id)
    {
        $enumerator = Enumerator::findOrFail($id);

        // Jika sudah punya user, tolak
        if ($enumerator->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Enumerator ini sudah memiliki akun user.',
            ], 422);
        }

        DB::transaction(function () use ($enumerator) {
            $telephone = $enumerator->telephone;
            $email     = $telephone . '@kawulohalal.id';

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'      => $enumerator->nama_lengkap,
                    'telephone' => $telephone,
                    'password'  => Hash::make('enumkh123'),
                    'role'      => 'enumerator',
                ]
            );

            // Assign role via Spatie Permission (hanya jika user baru dibuat)
            if ($user->wasRecentlyCreated) {
                $user->assignRole('enumerator');
            }

            $enumerator->update(['user_id' => $user->id]);
        });

        return response()->json([
            'success' => true,
            'message' => "User berhasil digenerate dengan email: {$enumerator->telephone}@kawulohalal.id",
        ]);
    }
}
