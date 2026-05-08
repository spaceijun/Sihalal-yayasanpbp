<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServerInfoController extends Controller
{
    /**
     * Tampilkan halaman Server Info.
     */
    public function index()
    {
        return view('superadmin.home.server-info');
    }

    /**
     * API endpoint: kembalikan data real-time server (JSON).
     * Dipanggil oleh JavaScript setiap beberapa detik.
     */
    public function realtime(Request $request)
    {
        return response()->json($this->collectMetrics());
    }

    // ──────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────

    private function collectMetrics(): array
    {
        return [
            'cpu'       => $this->cpuUsage(),
            'memory'    => $this->memoryInfo(),
            'disk'      => $this->diskInfo(),
            'load'      => $this->systemLoad(),
            'uptime'    => $this->uptime(),
            'server'    => $this->serverInfo(),
            'php'       => $this->phpInfo(),
            'network'   => $this->networkInfo(),
            'timestamp' => now()->format('H:i:s'),
        ];
    }

    // ── CPU ──────────────────────────────────────────────────────────

    private function cpuUsage(): array
    {
        $cores    = (int) shell_exec('nproc 2>/dev/null') ?: 1;
        $percent  = $this->getCpuPercent();
        $model    = trim(shell_exec("cat /proc/cpuinfo 2>/dev/null | grep 'model name' | head -1 | cut -d: -f2") ?: 'Unknown CPU');
        $freq     = $this->getCpuFrequency();
        $temp     = $this->getCpuTemp();

        return [
            'percent' => $percent,
            'cores'   => $cores,
            'model'   => $model,
            'freq'    => $freq,
            'temp'    => $temp,
            'per_core' => $this->getPerCoreCpu(),
        ];
    }

    private function getCpuPercent(): float
    {
        // Ambil dua snapshot /proc/stat dengan jeda singkat
        $stat1 = $this->readCpuStat();
        usleep(200000); // 200ms
        $stat2 = $this->readCpuStat();

        if (!$stat1 || !$stat2) {
            // Fallback: top
            $top = shell_exec("top -bn1 2>/dev/null | grep 'Cpu(s)' | awk '{print $2+$4}'");
            return $top ? round((float)$top, 1) : 0.0;
        }

        $diff = [];
        foreach ($stat1 as $k => $v) {
            $diff[$k] = ($stat2[$k] ?? 0) - $v;
        }

        $total = array_sum($diff);
        $idle  = $diff['idle'] + ($diff['iowait'] ?? 0);

        return $total > 0 ? round((1 - $idle / $total) * 100, 1) : 0.0;
    }

    private function readCpuStat(): ?array
    {
        $line = @file('/proc/stat')[0] ?? null;
        if (!$line || !str_starts_with($line, 'cpu ')) return null;

        $parts = preg_split('/\s+/', trim($line));
        $keys  = ['_', 'user', 'nice', 'system', 'idle', 'iowait', 'irq', 'softirq', 'steal'];
        $out   = [];
        foreach ($keys as $i => $k) {
            if ($k !== '_') $out[$k] = (int)($parts[$i] ?? 0);
        }
        return $out;
    }

    private function getCpuFrequency(): string
    {
        $freq = shell_exec("cat /proc/cpuinfo 2>/dev/null | grep 'cpu MHz' | head -1 | awk '{print $4}'");
        if ($freq) {
            $mhz = round((float)$freq);
            return $mhz >= 1000 ? round($mhz / 1000, 2) . ' GHz' : $mhz . ' MHz';
        }
        return 'N/A';
    }

    private function getCpuTemp(): ?float
    {
        // lm-sensors atau thermal_zone
        $temp = shell_exec("cat /sys/class/thermal/thermal_zone0/temp 2>/dev/null");
        if ($temp) return round((int)$temp / 1000, 1);

        $sensors = shell_exec("sensors 2>/dev/null | grep 'Core 0' | awk '{print $3}' | tr -d '+°C'");
        return $sensors ? round((float)$sensors, 1) : null;
    }

    private function getPerCoreCpu(): array
    {
        $lines = file('/proc/stat') ?: [];
        $cores = [];
        $prev  = Cache::get('cpu_stat_cores', []);
        $curr  = [];

        foreach ($lines as $line) {
            if (preg_match('/^cpu(\d+)\s+(.+)/', $line, $m)) {
                $id    = (int)$m[1];
                $parts = array_map('intval', preg_split('/\s+/', trim($m[2])));
                $curr[$id] = $parts;
            }
        }

        Cache::put('cpu_stat_cores', $curr, 5);

        foreach ($curr as $id => $parts) {
            $p = $prev[$id] ?? array_fill(0, count($parts), 0);
            $dtotal = array_sum($parts) - array_sum($p);
            $didle  = ($parts[3] - ($p[3] ?? 0)) + ($parts[4] - ($p[4] ?? 0));
            $cores[$id] = $dtotal > 0 ? round((1 - $didle / $dtotal) * 100, 1) : 0.0;
        }

        return $cores;
    }

    // ── MEMORY ──────────────────────────────────────────────────────

    private function memoryInfo(): array
    {
        $data = [];
        $lines = file('/proc/meminfo') ?: [];
        foreach ($lines as $line) {
            if (preg_match('/^(\w+):\s+(\d+)/', $line, $m)) {
                $data[$m[1]] = (int)$m[2]; // kB
            }
        }

        $total     = $data['MemTotal']     ?? 0;
        $available = $data['MemAvailable'] ?? 0;
        $used      = $total - $available;
        $cached    = ($data['Cached'] ?? 0) + ($data['Buffers'] ?? 0);
        $swap_total = $data['SwapTotal'] ?? 0;
        $swap_free  = $data['SwapFree']  ?? 0;
        $swap_used  = $swap_total - $swap_free;

        return [
            'total'      => $this->bytesToHuman($total * 1024),
            'used'       => $this->bytesToHuman($used * 1024),
            'available'  => $this->bytesToHuman($available * 1024),
            'cached'     => $this->bytesToHuman($cached * 1024),
            'percent'    => $total > 0 ? round($used / $total * 100, 1) : 0,
            'swap_total' => $this->bytesToHuman($swap_total * 1024),
            'swap_used'  => $this->bytesToHuman($swap_used * 1024),
            'swap_pct'   => $swap_total > 0 ? round($swap_used / $swap_total * 100, 1) : 0,
        ];
    }

    // ── DISK ────────────────────────────────────────────────────────

    private function diskInfo(): array
    {
        $total = disk_total_space('/');
        $free  = disk_free_space('/');
        $used  = $total - $free;

        return [
            'total'   => $this->bytesToHuman($total),
            'used'    => $this->bytesToHuman($used),
            'free'    => $this->bytesToHuman($free),
            'percent' => $total > 0 ? round($used / $total * 100, 1) : 0,
        ];
    }

    // ── LOAD ────────────────────────────────────────────────────────

    private function systemLoad(): array
    {
        $load = sys_getloadavg() ?: [0, 0, 0];
        return [
            '1'  => round($load[0], 2),
            '5'  => round($load[1], 2),
            '15' => round($load[2], 2),
        ];
    }

    // ── UPTIME ──────────────────────────────────────────────────────

    private function uptime(): string
    {
        $seconds = (int)(file_get_contents('/proc/uptime') ?: '0');
        $d = intdiv($seconds, 86400);
        $h = intdiv($seconds % 86400, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        $parts = [];
        if ($d > 0) $parts[] = "{$d}d";
        if ($h > 0) $parts[] = "{$h}h";
        if ($m > 0) $parts[] = "{$m}m";
        $parts[] = "{$s}s";

        return implode(' ', $parts);
    }

    // ── SERVER INFO ─────────────────────────────────────────────────

    private function serverInfo(): array
    {
        $os   = php_uname('s') . ' ' . php_uname('r');
        $host = gethostname() ?: config('CPANEL_DOMAIN', 'localhost');

        return [
            'os'       => $os,
            'hostname' => $host,
            'domain'   => env('CPANEL_DOMAIN', $host),
            'kernel'   => php_uname('r'),
            'arch'     => php_uname('m'),
            'ip'       => $_SERVER['SERVER_ADDR'] ?? gethostbyname($host),
            'web'      => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        ];
    }

    // ── PHP INFO ────────────────────────────────────────────────────

    private function phpInfo(): array
    {
        return [
            'version'      => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_exec'     => ini_get('max_execution_time') . 's',
            'upload_max'   => ini_get('upload_max_filesize'),
            'extensions'   => count(get_loaded_extensions()),
            'sapi'         => PHP_SAPI,
            'zend'         => zend_version(),
        ];
    }

    // ── NETWORK ─────────────────────────────────────────────────────

    private function networkInfo(): array
    {
        $rx = $tx = 0;
        $lines = file('/proc/net/dev') ?: [];
        foreach ($lines as $line) {
            if (preg_match('/^\s*(eth\d|ens\d+|enp\d|eno\d|wlan\d):\s+(\d+)(?:\s+\d+){7}\s+(\d+)/', $line, $m)) {
                $rx += (int)$m[2];
                $tx += (int)$m[3];
            }
        }
        return [
            'rx' => $this->bytesToHuman($rx),
            'tx' => $this->bytesToHuman($tx),
        ];
    }

    // ── HELPERS ─────────────────────────────────────────────────────

    private function bytesToHuman(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / (1024 ** $i), 2) . ' ' . ($units[$i] ?? 'TB');
    }
}
