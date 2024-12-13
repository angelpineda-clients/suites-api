<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Room;
use App\Models\Service;
use App\Models\Size;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {

    $manager = User::factory()->create([
      'name' => 'Manager',
      'email' => 'manager@test.com',
      'password' => 'pass1234',
    ]);

    $customer = User::factory()->create([
      'name' => 'Pepito',
      'email' => 'customer@test.com',
      'password' => 'pass1234',
    ]);

    $role_manager = Role::create(['name' => 'manager']);
    $role_admin = Role::create(['name' => 'admin']);
    $role_customer = Role::create(['name' => 'customer']);
    $role_visit = Role::create(['name' => 'visit']);

    $manager->assignRole($role_manager);
    $manager->assignRole($role_admin);

    Size::create(['name' => 'pequeño', 'alias' => 'xs']);
    Size::create(['name' => 'mediano', 'alias' => 'md']);

    Floor::create(['name' => 'planta baja', 'alias' => 'pb']);
    Floor::create(['name' => 'primer piso', 'alias' => 'p1']);

    Service::create(['name' => 'television']);
    Service::create(['name' => 'cocina']);
    Service::create(['name' => 'aire acondicionado']);

    $room1 =

      $rooms = ['Suite 1', 'Suite 2', 'Suite 3', 'Suite 4', 'Suite 5', 'Suite 6', 'Suite 7', 'Suite 8', 'Suite 9'];

    //'description' => 'Cuarto de ejemplo creado con datos aleatorios.',

    foreach ($rooms as $room) {
      Room::create([
        'name' => $room,
        'price' => rand(2200, 6500),
        'capacity' => rand(2, 9),
        'beds' => rand(1, 7),
        'description' => 'Cuarto de ejemplo creado con datos aleatorios. '
      ]);
    }

  }
}
