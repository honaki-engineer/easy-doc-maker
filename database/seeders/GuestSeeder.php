<?php 

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuestSeeder extends Seeder
{
    public function run(): void
    {
        $guest = User::firstOrCreate(
            ['email' => 'guest@example.com'],
            [
                'name' => 'ゲスト',
                'password' => Hash::make(config('app.guest_password')),
            ]
        );


    }
}


?>