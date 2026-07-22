<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WilayahService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function __construct(
        private WilayahService $wilayahService
    ) {}

    /**
     * Get all provinces
     *
     * @return JsonResponse
     */
    public function provinces(): JsonResponse
    {
        $provinces = $this->wilayahService->getProvinces();

        return response()->json([
            'success' => true,
            'data' => $provinces,
        ]);
    }

    /**
     * Get regencies by province code
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function regencies(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $regencies = $this->wilayahService->getRegencies($request->code);

        return response()->json([
            'success' => true,
            'data' => $regencies,
        ]);
    }

    /**
     * Get districts by regency code
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function districts(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $districts = $this->wilayahService->getDistricts($request->code);

        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    /**
     * Get villages by district code
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function villages(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $villages = $this->wilayahService->getVillages($request->code);

        return response()->json([
            'success' => true,
            'data' => $villages,
        ]);
    }

    /**
     * Get postal code + coordinates by village name with disambiguation.
     * Matches by kelurahan, kecamatan, and kabupaten for accurate results.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function kodePos(Request $request): JsonResponse
    {
        $request->validate([
            'kelurahan'  => 'required|string',
            'kecamatan'  => 'nullable|string',
            'kabupaten'  => 'nullable|string',
        ]);

        $result = $this->wilayahService->getKodePos(
            $request->input('kelurahan'),
            $request->input('kecamatan', ''),
            $request->input('kabupaten', '')
        );

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }
}
