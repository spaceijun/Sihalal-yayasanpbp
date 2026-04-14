<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataBank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DatabankController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DataBank::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:data_banks,code'],
        ]);

        $validated['code'] = Str::upper($validated['code']);
        $bank = DataBank::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data bank berhasil ditambahkan.',
            'data'    => $bank,
        ], 201);
    }

    public function show(DataBank $dataBank): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $dataBank,
        ]);
    }

    public function update(Request $request, DataBank $dataBank): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('data_banks', 'code')->ignore($dataBank->id),
            ],
        ]);

        if (isset($validated['code'])) {
            $validated['code'] = Str::upper($validated['code']);
        }

        $dataBank->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data bank berhasil diperbarui.',
            'data'    => $dataBank->fresh(),
        ]);
    }

    public function destroy(DataBank $dataBank): JsonResponse
    {
        $dataBank->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data bank berhasil dihapus.',
        ]);
    }
}
