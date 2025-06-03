<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Configure SQLite connection
Config::set('database.connections.sqlite', [
  'driver' => 'sqlite',
  'database' => database_path('database.sqlite'),
  'prefix' => '',
]);

// Configure MySQL connection with explicit values
Config::set('database.connections.mysql', [
  'driver' => 'mysql',
  'host' => '127.0.0.1',
  'port' => '3306',
  'database' => 'talentforge',  // Explicit database name
  'username' => 'root',
  'password' => '',
  'charset' => 'utf8mb4',
  'collation' => 'utf8mb4_unicode_ci',
  'prefix' => '',
]);

// Set queue and session drivers to use database
Config::set('queue.default', 'database');
Config::set('session.driver', 'database');

// Clear connection cache
DB::purge('sqlite');
DB::purge('mysql');

// Verify MySQL connection
try {
  DB::connection('mysql')->getPdo();
  echo "MySQL connection successful\n";
} catch (\Exception $e) {
  die("MySQL connection failed: " . $e->getMessage() . "\n");
}

// Get all tables
$tables = DB::connection('sqlite')->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

foreach ($tables as $table) {
  $tableName = $table->name;

  // Skip migrations table
  if ($tableName === 'migrations') continue;

  echo "Migrating table: {$tableName}\n";

  // Get all data from SQLite
  $data = DB::connection('sqlite')->table($tableName)->get();

  if (count($data) > 0) {
    // Insert into MySQL
    foreach ($data as $row) {
      try {
        DB::connection('mysql')->table($tableName)->insert((array) $row);
      } catch (\Exception $e) {
        echo "Error inserting into {$tableName}: " . $e->getMessage() . "\n";
      }
    }
  }

  echo "Completed {$tableName}\n";
}

echo "Migration completed!\n";
