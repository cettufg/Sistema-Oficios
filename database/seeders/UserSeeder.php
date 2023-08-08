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
        $user = User::where('email', 'allysson.jhonnatha@gmail.com')->first();
        if(!$user) {
            $user = User::create([
                'name' => 'Administrador',
                'email' => 'allysson.jhonnatha@gmail.com',
                'password' => Hash::make('12345678'),
                'status' => 1,
                'is_admin' => 1,
                'password_reset' => 0,
            ]);
        }

        $groupUser = GroupUsers::where('nome', 'Diretoria')->first();

        if(!$groupUser) {
            $groupUser = GroupUsers::create([
                'nome' => 'Diretoria',
                'descricao' => 'Grupo de usuários da diretoria',
                'status' => 1,
            ]);
        }

        $userGroup = UserGroup::where('user_id', $user->id)->where('group_id', $groupUser->id)->first();

        if(!$userGroup) {
            UserGroup::create([
                'user_id' => $user->id,
                'group_id' => $groupUser->id,
            ]);
        }
    }
}
