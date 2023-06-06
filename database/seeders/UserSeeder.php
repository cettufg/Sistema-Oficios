<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\GroupUsers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'allysson.jhonnatha@gmail.com',
            'password' => Hash::make('12345678'),
            'status' => 1,
            'is_admin' => 1,
            'password_reset' => 0,
        ]);

        GroupUsers::create([
            'nome' => 'Diretoria',
            'descricao' => 'Grupo de usuários da diretoria',
            'status' => 1,
        ]);

        UserGroup::create([
            'user_id' => 1,
            'group_id' => 1,
        ]);
    }
}
