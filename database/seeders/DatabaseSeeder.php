<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Existing stores are upgraded through forward migrations.
        // Provision the first administrator with admin:manage; author catalog data in /admin.
    }
}
