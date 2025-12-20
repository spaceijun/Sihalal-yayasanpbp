<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\DataLapanganRequest;
use App\Models\Enumerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DataLapanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DataLapangan::with('enumerator');

        // Filter berdasarkan nama_pu
        if ($request->filled('nama_pu')) {
            $query->where('nama_pu', 'like', '%' . $request->nama_pu . '%');
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $dataLapangans = $query->paginate(10);
        $i = ($dataLapangans->currentPage() - 1) * $dataLapangans->perPage();

        return view('superadmin.data-lapangan.index', compact('dataLapangans', 'i'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $dataLapangan = new DataLapangan();
        $enumerators = Enumerator::all();

        return view('publik.form', compact('dataLapangan', 'enumerators'));
    }

    /**
     * Upload a file based on the given file type
     *
     * @param Request $request
     * @param DataLapangan $dataLapangan
     * @return RedirectResponse
     */
    /**
     * Validation rules:
     * - file: required, must be a PDF, max size of 5MB
     * - file_type: required, must be either 'oss' or 'sihalal'
     *
     * If the file is uploaded successfully, the status of the data lapangan will be updated
     * based on the file type. If the file type is 'oss', the status will be updated to 'PROGRESS OSS'.
     * If the file type is 'sihalal', the status will be updated to 'TERBIT SH'.
     */
    public function uploadFile(Request $request, DataLapangan $dataLapangan)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120', // 5MB
            'file_type' => 'required|in:oss,sihalal'
        ]);

        $fileType = $request->file_type;
        $fieldName = 'file_' . $fileType;

        // Delete old file if exists
        if ($dataLapangan->$fieldName) {
            Storage::delete($dataLapangan->$fieldName);
        }

        // Upload new file
        $path = $request->file('file')->store('files/' . $fileType, 'public');
        $dataLapangan->$fieldName = $path;

        // Update status based on file type
        if ($fileType == 'oss') {
            $dataLapangan->status = 'PROGRESS OSS';
            $statusMessage = 'Status diubah menjadi PROGRESS OSS';
        } elseif ($fileType == 'sihalal') {
            $dataLapangan->status = 'TERBIT SH';
            $statusMessage = 'Status diubah menjadi TERBIT SH';
        }

        $dataLapangan->save();

        return redirect()->back()->with('success', 'File ' . strtoupper($fileType) . ' berhasil diupload. ' . $statusMessage);
    }

    /**
     * Delete a file based on the given file type
     *
     * @param Request $request
     * @param DataLapangan $dataLapangan
     * @return RedirectResponse
     */
    public function deleteFile(Request $request, DataLapangan $dataLapangan)
    {
        $fileType = $request->file_type;
        $fieldName = 'file_' . $fileType;

        if ($dataLapangan->$fieldName) {
            Storage::delete($dataLapangan->$fieldName);
            $dataLapangan->$fieldName = null;

            // Update status when file is deleted
            if ($fileType == 'oss') {
                $dataLapangan->status = 'PENDING';
                $statusMessage = 'Status dikembalikan ke PENDING';
            } elseif ($fileType == 'sihalal') {
                // If SIHALAL file is deleted, check if OSS file exists
                if ($dataLapangan->file_oss) {
                    $dataLapangan->status = 'PROGRESS OSS';
                    $statusMessage = 'Status dikembalikan ke PROGRESS OSS';
                } else {
                    $dataLapangan->status = 'PENDING';
                    $statusMessage = 'Status dikembalikan ke PENDING';
                }
            }

            $dataLapangan->save();

            return redirect()->back()->with('success', 'File ' . strtoupper($fileType) . ' berhasil dihapus. ' . $statusMessage);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan');
    }

    public function updateStatus(Request $request, DataLapangan $dataLapangan)
    {
        $request->validate([
            'status' => 'required|in:PENDING,PROGRESS OSS,PROGRESS SIHALAL,TERBIT SH'
        ]);

        $oldStatus = $dataLapangan->status;
        $newStatus = $request->status;

        // Update status
        $dataLapangan->status = $newStatus;
        $dataLapangan->save();

        // Buat pesan success
        $message = "Status berhasil diubah dari <strong>{$oldStatus}</strong> menjadi <strong>{$newStatus}</strong>";

        return redirect()->back()->with('success', $message);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(DataLapanganRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        // Handle foto_ktp
        if ($request->hasFile('foto_ktp')) {
            $image = $request->file('foto_ktp');
            $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->storeAs('ktp', $imageName, 'public');
            $validatedData['foto_ktp'] = 'fotoktp/' . $imageName;
        }

        // Handle foto_rumah
        if ($request->hasFile('foto_rumah')) {
            $image = $request->file('foto_rumah');
            $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->storeAs('rumah', $imageName, 'public');
            $validatedData['foto_rumah'] = 'foto-rumah/' . $imageName;
        }

        // Handle foto_pendamping
        if ($request->hasFile('foto_pendamping')) {
            $image = $request->file('foto_pendamping');
            $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->storeAs('pendamping', $imageName, 'public');
            $validatedData['foto_pendamping'] = 'foto-pendamping/' . $imageName;
        }

        // Handle foto_proses
        if ($request->hasFile('foto_proses')) {
            $image = $request->file('foto_proses');
            $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->storeAs('proses', $imageName, 'public');
            $validatedData['foto_proses'] = 'foto-proses/' . $imageName;
        }

        // Handle foto_produk
        if ($request->hasFile('foto_produk')) {
            $image = $request->file('foto_produk');
            $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->storeAs('produk', $imageName, 'public');
            $validatedData['foto_produk'] = 'foto-produk/' . $imageName;
        }

        DataLapangan::create($validatedData);

        return Redirect::route('formulir.halal')
            ->with('success', 'DataLapangan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $dataLapangan = DataLapangan::find($id);

        return view('superadmin.data-lapangan.show', compact('dataLapangan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $dataLapangan = DataLapangan::find($id);

        return view('superadmin.data-lapangan.edit', compact('dataLapangan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DataLapanganRequest $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $dataLapangan->update($request->validated());

        return Redirect::route('superadmin.data-lapangans.index')
            ->with('success', 'DataLapangan updated successfully');
    }

    public function downloadFotoRumahPdf($id)
    {
        $dataLapangan = DataLapangan::findOrFail($id);

        // Path foto rumah
        $fotoPath = storage_path('storage/' . $dataLapangan->foto_rumah);

        // Check if file exists
        if (!file_exists($fotoPath)) {
            return back()->with('error', 'Foto rumah tidak ditemukan');
        }

        // Convert image to base64
        $imageData = base64_encode(file_get_contents($fotoPath));
        $imageMimeType = mime_content_type($fotoPath);
        $imageSrc = 'data:' . $imageMimeType . ';base64,' . $imageData;

        // Data untuk PDF
        $data = [
            'dataLapangan' => $dataLapangan,
            'imageSrc' => $imageSrc,
            'tanggal_cetak' => now()->format('d-m-Y H:i:s')
        ];

        // Generate PDF
        $pdf = Pdf::loadView('superadmin.data-lapangan.foto-rumah-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        // Download PDF
        $filename = 'Foto_Rumah_' . $dataLapangan->nama_pu . '_' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }


    public function destroy($id): RedirectResponse
    {
        DataLapangan::find($id)->delete();

        return Redirect::route('superadmin.data-lapangans.index')
            ->with('success', 'DataLapangan deleted successfully');
    }
}
