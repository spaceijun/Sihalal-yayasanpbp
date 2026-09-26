<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WrgroupNihilRequest;
use App\Models\Superadmin\WrgroupOutbox;
use App\Services\Integrasi\Wrgroup\WrgroupDeliveryService;
use App\Services\Integrasi\Wrgroup\WrgroupEventService;
use App\Services\Integrasi\Wrgroup\WrgroupMonitorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

/**
 * Panel monitoring integrasi WRGROUP Super Apps (satu arah, Kawulo Halal → WRGROUP).
 */
class WrgroupController extends Controller
{
    public function __construct(
        protected WrgroupMonitorService $monitor,
        protected WrgroupDeliveryService $delivery,
        protected WrgroupEventService $events,
    ) {}

    public function index(): View
    {
        return view('superadmin.wrgroup.index', [
            'dashboard' => $this->monitor->dashboard(),
        ]);
    }

    /**
     * DataTables JSON untuk daftar event outbox.
     */
    public function data(Request $request)
    {
        $query = $this->monitor->eventsQuery($request->only(['status', 'endpoint']));

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('waktu', fn (WrgroupOutbox $row) => optional($row->event_time)->format('d/m/Y H:i') ?? '—')
            ->addColumn('endpoint_label', fn (WrgroupOutbox $row) => $row->endpoint_label)
            ->addColumn('status_badge', function (WrgroupOutbox $row) {
                return '<span class="adm-badge '.$row->status_badge_class.'">'.$row->status_label.'</span>';
            })
            ->addColumn('http', fn (WrgroupOutbox $row) => $row->last_http_status ?: '—')
            ->addColumn('aksi', function (WrgroupOutbox $row) {
                $showUrl = route('superadmin.wrgroup.show', $row->hashed_id);
                $html = '<a class="adm-btn primary icon-only" href="'.$showUrl.'" title="Detail">
                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </a>';

                if ($row->isRetryable()) {
                    $retryUrl = route('superadmin.wrgroup.retry', $row->hashed_id);
                    $html .= ' <button type="button" class="adm-btn warning icon-only" title="Kirim Ulang"
                        onclick="kirimUlangEvent(\''.$retryUrl.'\')">
                        <svg viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    </button>';
                }

                return $html;
            })
            ->rawColumns(['status_badge', 'aksi'])
            ->make(true);
    }

    public function show(WrgroupOutbox $event): View
    {
        return view('superadmin.wrgroup.show', ['event' => $event]);
    }

    public function retry(WrgroupOutbox $event): RedirectResponse
    {
        if (! $this->delivery->retry($event)) {
            return back()->with('warning', 'Hanya event berstatus Ditolak / Gagal yang bisa dikirim ulang.');
        }

        return back()->with('success', 'Event masuk antrian untuk dikirim ulang (event_id sama).');
    }

    public function heartbeat(): RedirectResponse
    {
        $result = $this->delivery->heartbeat();

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function process(): RedirectResponse
    {
        $summary = $this->delivery->processDue();

        if ($summary['skipped']) {
            return back()->with('warning', $summary['skipped']);
        }

        return back()->with('info', sprintf(
            'Diproses %d event: terkirim %d, menunggu %d, ditolak %d, gagal %d.',
            $summary['processed'], $summary['sent'], $summary['pending'], $summary['rejected'], $summary['failed'],
        ));
    }

    public function resume(): RedirectResponse
    {
        $this->delivery->resume();

        return back()->with('success', 'Pengiriman dilanjutkan. Event pending akan diproses scheduler.');
    }

    public function nihil(WrgroupNihilRequest $request): RedirectResponse
    {
        $result = $this->events->reportNihil($request->validated('periode'));

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }
}
