<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'database',
    ];

    public function configure()
    {
        Config::set('database.connections.tenant', Config::get('database.connections.owner'));
        Config::set('database.connections.tenant.database', $this->database);

        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    public function createDatabase()
    {
        $exists = DB::select("SELECT 1 FROM pg_database WHERE datname = ?", [$this->database]);

        if (empty($exists)) {
            $query = "CREATE DATABASE \"{$this->database}\"";
            DB::statement($query);
        }
    }

    public function dropDatabase()
    {
        $exists = DB::select("SELECT 1 FROM pg_database WHERE datname = ?", [$this->database]);

        if (!empty($exists)) {
            $query = "DROP DATABASE \"{$this->database}\"";
            DB::statement($query);
        }
    }
}