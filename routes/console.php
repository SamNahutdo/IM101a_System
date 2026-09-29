<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;




Artisan::command('db:automation', function () {
    $this->info('Installing database triggers, stored procedures, functions, and views...');
    $path = database_path('sql/database_automation.sql');
    if (!file_exists($path)) {
        $this->error("SQL automation file not found at: {$path}");
        return 1;
    }
    
    $content = file_get_contents($path);
    $content = preg_replace('/USE\s+falcon_system\s*;/i', '', $content);
    
    $lines = explode("\n", $content);
    $currentDelimiter = ';';
    $buffer = '';
    $commands = [];
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (preg_match('/^DELIMITER\s+(.+)$/i', $trimmed, $m)) {
            $currentDelimiter = trim($m[1]);
            continue;
        }
        if (empty($trimmed) || str_starts_with($trimmed, '--')) {
            continue;
        }
        $buffer .= $line . "\n";
        if (str_ends_with($trimmed, $currentDelimiter)) {
            $stmt = substr(trim($buffer), 0, -strlen($currentDelimiter));
            if (!empty(trim($stmt))) {
                $commands[] = trim($stmt);
            }
            $buffer = '';
        }
    }
    if (!empty(trim($buffer))) {
        $commands[] = trim($buffer);
    }
    
    foreach ($commands as $cmd) {
        DB::unprepared($cmd);
    }
    
    $this->info('Successfully installed ' . count($commands) . ' database automation statements.');
    return 0;
})->purpose('Install MySQL triggers, procedures, functions, and views');

Artisan::command('test:defense', function () {
    require_once base_path('tests/FalconSystemTestSuite.php');
    $suite = new \Tests\FalconSystemTestSuite();
    return $suite->run();
})->purpose('Run the 6 automated defense verification tests');
