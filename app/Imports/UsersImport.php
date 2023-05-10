<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $name = str_replace('Ñ', 'ñ', $row[5]);
        $name = strtolower($name);
        $email = str_replace(' ', '_', $name) . '@blapp.com';

        if (!User::where('email', $email)->first()) {
            return new User([
                'name' => $row[5],
                'email' => $email,
                'password' => $email . '123',
                'role' => 'gestor',
                'entity' => 'chihuahua',
            ]);
        }
    }
}
