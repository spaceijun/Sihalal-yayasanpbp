<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Models\TicketPendamping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * GET /api/enumerator/tiket
     * List semua tiket keluhan milik enumerator yang login.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $query = TicketPendamping::where('user_id', $user->id)
            ->with('dataLapangan:id,nama_pu')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'status'  => true,
            'message' => 'Berhasil mengambil data tiket',
            'data'    => $tickets->map(fn ($t) => $this->format($t)),
            'meta'    => [
                'current_page' => $tickets->currentPage(),
                'last_page'    => $tickets->lastPage(),
                'per_page'     => $tickets->perPage(),
                'total'        => $tickets->total(),
            ],
        ]);
    }

    /**
     * POST /api/enumerator/tiket
     * Buat tiket keluhan baru.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'data_lapangan_id' => 'nullable|integer|exists:data_lapangans,id',
            'isi_kendala'      => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Pastikan data_lapangan milik enumerator yang login
        if ($request->filled('data_lapangan_id')) {
            $enumeratorId = $user->enumerator?->id;
            $valid = DataLapangan::where('id', $request->data_lapangan_id)
                ->where('enumerator_id', $enumeratorId)
                ->exists();

            if (!$valid) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data lapangan tidak ditemukan atau bukan milik Anda',
                ], 403);
            }
        }

        $ticket = TicketPendamping::create([
            'user_id'          => $user->id,
            'data_lapangan_id' => $request->data_lapangan_id,
            'no_tiket'         => $this->generateNoTiket(),
            'isi_kendala'      => $request->isi_kendala,
            'status'           => 'Open',
        ]);

        $ticket->load('dataLapangan:id,nama_pu');

        return response()->json([
            'status'  => true,
            'message' => 'Tiket keluhan berhasil dibuat',
            'data'    => $this->format($ticket),
        ], 201);
    }

    /**
     * GET /api/enumerator/tiket/{id}
     * Detail tiket milik enumerator yang login.
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::user();

        $ticket = TicketPendamping::where('user_id', $user->id)
            ->with('dataLapangan:id,nama_pu')
            ->findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Berhasil mengambil detail tiket',
            'data'    => $this->format($ticket),
        ]);
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────

    private function format(TicketPendamping $ticket): array
    {
        return [
            'id'          => $ticket->id,
            'no_tiket'    => $ticket->no_tiket,
            'nama_pu'     => $ticket->dataLapangan?->nama_pu,
            'isi_kendala' => $ticket->isi_kendala,
            'status'      => $ticket->status,
            'created_at'  => $ticket->created_at?->toDateTimeString(),
        ];
    }

    private function generateNoTiket(): string
    {
        $prefix = 'TPD-' . date('Ymd') . '-';
        $last   = TicketPendamping::where('no_tiket', 'like', $prefix . '%')
            ->orderByDesc('no_tiket')
            ->value('no_tiket');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
