<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * ServerInfoController
 *
 *     Semua data dibaca langsung dari /proc/* via file_get_contents()
 *     agar kompatibel dengan shared hosting yang menonaktifkan shell functions.
 */
class ServerInfoController extends Controller
{
    public function index()
    {
        return view('superadmin.home.server-info');
    }

    public function realtime(Request $request)
    {
        return response()->json($this->collectMetrics());
    }

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
            'stack'     => $this->stackVersions(),
            'timestamp' => \now()->format('H:i:s'),
        ];
    }

    // ── CPU ──────────────────────────────────────────────────────────

    private function cpuUsage(): array
    {
        return [
            'percent'  => $this->getCpuPercent(),
            'cores'    => $this->getCpuCores(),
            'model'    => $this->getCpuModel(),
            'freq'     => $this->getCpuFrequency(),
            'temp'     => $this->getCpuTemp(),
            'per_core' => $this->getPerCoreCpu(),
        ];
    }

    private function getCpuPercent(): float
    {
        $s1 = $this->readCpuStat('cpu');
        \usleep(200000);
        $s2 = $this->readCpuStat('cpu');

        if (!$s1 || !$s2) return 0.0;

        $total = \array_sum($s2) - \array_sum($s1);
        $idle  = ($s2[3] - $s1[3]) + ($s2[4] - $s1[4]);

        return $total > 0 ? \round((1 - $idle / $total) * 100, 1) : 0.0;
    }

    private function readCpuStat(string $key): ?array
    {
        $content = @\file_get_contents('/proc/stat');
        if (!$content) return null;

        foreach (\explode("\n", $content) as $line) {
            if (\str_starts_with($line, $key . ' ')) {
                $parts = \preg_split('/\s+/', \trim($line));
                \array_shift($parts);
                return \array_map('intval', $parts);
            }
        }
        return null;
    }

    private function getCpuCores(): int
    {
        $content = @\file_get_contents('/proc/cpuinfo');
        if (!$content) return 1;
        return \substr_count($content, "processor\t:");
    }

    private function getCpuModel(): string
    {
        $content = @\file_get_contents('/proc/cpuinfo');
        if ($content && \preg_match('/model name\s*:\s*(.+)/i', $content, $m)) {
            return \trim($m[1]);
        }
        return 'Unknown CPU';
    }

    private function getCpuFrequency(): string
    {
        $content = @\file_get_contents('/proc/cpuinfo');
        if ($content && \preg_match('/cpu MHz\s*:\s*([\d.]+)/i', $content, $m)) {
            $mhz = \round((float)$m[1]);
            return $mhz >= 1000 ? \round($mhz / 1000, 2) . ' GHz' : $mhz . ' MHz';
        }
        return 'N/A';
    }

    private function getCpuTemp(): ?float
    {
        $raw = @\file_get_contents('/sys/class/thermal/thermal_zone0/temp');
        if ($raw !== false && \is_numeric(\trim($raw))) {
            return \round((int)\trim($raw) / 1000, 1);
        }
        return null;
    }

    private function getPerCoreCpu(): array
    {
        $content = @\file_get_contents('/proc/stat');
        if (!$content) return [];

        $prev = Cache::get('cpu_stat_cores', []);
        $curr = [];

        foreach (\explode("\n", $content) as $line) {
            if (\preg_match('/^cpu(\d+)\s+(.+)/', $line, $m)) {
                $id        = (int)$m[1];
                $curr[$id] = \array_map('intval', \preg_split('/\s+/', \trim($m[2])));
            }
        }

        Cache::put('cpu_stat_cores', $curr, 5);

        $result = [];
        foreach ($curr as $id => $parts) {
            $p      = $prev[$id] ?? \array_fill(0, \count($parts), 0);
            $dtotal = \array_sum($parts) - \array_sum($p);
            $didle  = ($parts[3] - ($p[3] ?? 0)) + ($parts[4] - ($p[4] ?? 0));
            $result[$id] = $dtotal > 0 ? \round((1 - $didle / $dtotal) * 100, 1) : 0.0;
        }

        return $result;
    }

    // ── MEMORY ──────────────────────────────────────────────────────

    private function memoryInfo(): array
    {
        $raw  = @\file_get_contents('/proc/meminfo');
        $data = [];

        if ($raw) {
            foreach (\explode("\n", $raw) as $line) {
                if (\preg_match('/^(\w+):\s+(\d+)/', $line, $m)) {
                    $data[$m[1]] = (int)$m[2];
                }
            }
        }

        $total     = $data['MemTotal']     ?? 0;
        $available = $data['MemAvailable'] ?? ($data['MemFree'] ?? 0);
        $used      = $total - $available;
        $cached    = ($data['Cached'] ?? 0) + ($data['Buffers'] ?? 0);
        $swapTotal = $data['SwapTotal'] ?? 0;
        $swapFree  = $data['SwapFree']  ?? 0;
        $swapUsed  = $swapTotal - $swapFree;

        return [
            'total'      => $this->bytesToHuman($total * 1024),
            'used'       => $this->bytesToHuman($used * 1024),
            'available'  => $this->bytesToHuman($available * 1024),
            'cached'     => $this->bytesToHuman($cached * 1024),
            'percent'    => $total > 0 ? \round($used / $total * 100, 1) : 0,
            'swap_total' => $this->bytesToHuman($swapTotal * 1024),
            'swap_used'  => $this->bytesToHuman($swapUsed * 1024),
            'swap_pct'   => $swapTotal > 0 ? \round($swapUsed / $swapTotal * 100, 1) : 0,
        ];
    }

    // ── DISK ────────────────────────────────────────────────────────

    private function diskInfo(): array
    {
        $total = (int)@\disk_total_space('/');
        $free  = (int)@\disk_free_space('/');
        $used  = $total - $free;

        return [
            'total'   => $this->bytesToHuman($total),
            'used'    => $this->bytesToHuman($used),
            'free'    => $this->bytesToHuman($free),
            'percent' => $total > 0 ? \round($used / $total * 100, 1) : 0,
        ];
    }

    // ── LOAD ────────────────────────────────────────────────────────

    private function systemLoad(): array
    {
        $load = @\sys_getloadavg() ?: [0, 0, 0];
        return [
            '1'  => \round($load[0], 2),
            '5'  => \round($load[1], 2),
            '15' => \round($load[2], 2),
        ];
    }

    // ── UPTIME ──────────────────────────────────────────────────────

    private function uptime(): string
    {
        $raw     = @\file_get_contents('/proc/uptime');
        $seconds = $raw ? (int)\explode(' ', \trim($raw))[0] : 0;

        $d = \intdiv($seconds, 86400);
        $h = \intdiv($seconds % 86400, 3600);
        $m = \intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        $parts = [];
        if ($d > 0) $parts[] = "{$d}d";
        if ($h > 0) $parts[] = "{$h}h";
        if ($m > 0) $parts[] = "{$m}m";
        $parts[] = "{$s}s";

        return \implode(' ', $parts);
    }

    // ── SERVER INFO ─────────────────────────────────────────────────

    private function serverInfo(): array
    {
        $host = @\gethostname() ?: 'localhost';

        return [
            'os'       => \php_uname('s') . ' ' . \php_uname('r'),
            'hostname' => $host,
            'domain'   => parse_url(config('app.url'), PHP_URL_HOST) ?? request()->getHost() ?? $host,
            'kernel'   => \php_uname('r'),
            'arch'     => \php_uname('m'),
            'ip'       => $_SERVER['SERVER_ADDR'] ?? @\gethostbyname($host) ?? 'N/A',
            'web'      => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        ];
    }

    // ── PHP INFO ────────────────────────────────────────────────────

    private function phpInfo(): array
    {
        return [
            'version'      => PHP_VERSION,
            'memory_limit' => \ini_get('memory_limit'),
            'max_exec'     => \ini_get('max_execution_time') . 's',
            'upload_max'   => \ini_get('upload_max_filesize'),
            'extensions'   => \count(\get_loaded_extensions()),
            'sapi'         => PHP_SAPI,
            'zend'         => \zend_version(),
        ];
    }

    // ── NETWORK ─────────────────────────────────────────────────────

    private function networkInfo(): array
    {
        $rx  = $tx = 0;
        $raw = @\file_get_contents('/proc/net/dev');

        if ($raw) {
            foreach (\explode("\n", $raw) as $line) {
                if (\preg_match(
                    '/^\s*(eth\d+|ens\d+|enp\d+\w*|eno\d+|wlan\d+|venet\d+):\s+(\d+)(?:\s+\d+){7}\s+(\d+)/',
                    $line,
                    $m
                )) {
                    $rx += (int)$m[2];
                    $tx += (int)$m[3];
                }
            }
        }

        return [
            'rx' => $this->bytesToHuman($rx),
            'tx' => $this->bytesToHuman($tx),
        ];
    }


    // ── STACK VERSIONS ──────────────────────────────────────────────

    private function stackVersions(): array
    {
        return [
            'php'     => $this->versionPhp(),
            'laravel' => $this->versionLaravel(),
            'mysql'   => $this->versionMysql(),
            'server'  => $this->versionWebServer(),
        ];
    }

    private function versionPhp(): array
    {
        return [
            'label'   => 'PHP',
            'version' => PHP_VERSION,
            'icon'    => 'php',
            'color'   => '#777BB4',
            'bg'      => 'rgba(119,123,180,0.1)',
        ];
    }

    private function versionLaravel(): array
    {
        $version = 'N/A';
        try {
            $lockPath = \base_path('composer.lock');
            if (\file_exists($lockPath)) {
                $lock = \json_decode(\file_get_contents($lockPath), true);
                foreach ($lock['packages'] ?? [] as $pkg) {
                    if ($pkg['name'] === 'laravel/framework') {
                        $version = \ltrim($pkg['version'], 'v');
                        break;
                    }
                }
            }
            if ($version === 'N/A') {
                $version = \Illuminate\Foundation\Application::VERSION;
            }
        } catch (\Throwable $e) {
            $version = 'N/A';
        }
        return [
            'label'   => 'Laravel',
            'version' => $version,
            'icon'    => 'laravel',
            'color'   => '#FF2D20',
            'bg'      => 'rgba(255,45,32,0.1)',
        ];
    }

    private function versionMysql(): array
    {
        $version = 'N/A';
        $label   = 'MySQL';
        try {
            $result  = DB::selectOne('SELECT VERSION() as v');
            $version = $result->v ?? 'N/A';
            $label   = \str_contains(\strtolower($version), 'mariadb') ? 'MariaDB' : 'MySQL';
        } catch (\Throwable $e) {
            $version = 'N/A';
        }
        return [
            'label'   => $label,
            'version' => $version,
            'icon'    => 'mysql',
            'color'   => '#00758F',
            'bg'      => 'rgba(0,117,143,0.1)',
        ];
    }

    private function versionWebServer(): array
    {
        $software = $_SERVER['SERVER_SOFTWARE'] ?? '';
        $label = 'Web Server';
        $color = '#E44D26';
        $bg = 'rgba(228,77,38,0.1)';
        $icon = 'server';
        if (\str_contains(\strtolower($software), 'apache')) {
            $label = 'Apache';
            $color = '#D22128';
            $bg = 'rgba(210,33,40,0.1)';
            $icon = 'apache';
        } elseif (\str_contains(\strtolower($software), 'nginx')) {
            $label = 'Nginx';
            $color = '#009900';
            $bg = 'rgba(0,153,0,0.1)';
            $icon = 'nginx';
        } elseif (\str_contains(\strtolower($software), 'litespeed')) {
            $label = 'LiteSpeed';
            $color = '#0095D9';
            $bg = 'rgba(0,149,217,0.1)';
            $icon = 'server';
        }
        $version = 'N/A';
        if (\preg_match('/[\\w]+\/([\\d.]+)/i', $software, $m)) $version = $m[1];
        return [
            'label'   => $label,
            'version' => $version ?: ($software ?: 'N/A'),
            'icon'    => $icon,
            'color'   => $color,
            'bg'      => $bg,
        ];
    }

    // ── HELPERS ─────────────────────────────────────────────────────

    private function bytesToHuman(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i     = (int)\floor(\log($bytes, 1024));
        $i     = \min($i, \count($units) - 1);
        return \round($bytes / (1024 ** $i), 2) . ' ' . $units[$i];
    }
}
