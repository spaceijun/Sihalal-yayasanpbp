<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Service extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    /**
     * Handle the command.
     *
     * @param  string  $name
     * @return  int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $name = str_replace('\\', '/', $name);
        $parts = explode('/', $name);
        $className = array_pop($parts);

        // Build namespace
        if (empty($parts)) {
            $namespace = 'App\Services';
        } else {
            $namespace = 'App\Services\\' . implode('\\', $parts);
        }

        // Path file
        $path = app_path("Services/{$name}.php");
        // Buat direktori
        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Cek apakah sudah ada
        if (File::exists($path)) {
            $this->error("Service [{$name}] already exists!");
            return Command::FAILURE;
        }

        // Build file content
        $lines = [
            '<?php',
            '',
            "namespace {$namespace};",
            '',
            "class {$className}",
            '{',
            '    /**',
            '     * Create a new service instance.',
            '     */',
            '    public function __construct()',
            '    {',
            '        //',
            '    }',
            '}',
            ''
        ];

        $content = implode(PHP_EOL, $lines);
        File::put($path, $content);

        $this->info("Service created successfully!");
        return Command::SUCCESS;
    }
}
