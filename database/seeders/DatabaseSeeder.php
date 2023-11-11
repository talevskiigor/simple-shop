<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->truncate();

        $user = \App\Models\User::factory(10)->create();
         $user = \App\Models\User::factory()->create([
             'name' => 'admin',
             'email' => 'admin@doman.tld',
             'password'=>bcrypt('admin')
         ]);


        // php artisan db:seed --class=OCSeeder
         $this->call(OCSeeder::class);
    }
}
