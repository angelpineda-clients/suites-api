<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Price;
use App\Models\Room;
use App\Models\Season;
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

    // 7 seasons
    Season::create(["name" => 'Navidad', 'alias' => 'xmas', 'initial_date' => '2024-12-05', 'final_date' => '2024-12-31']);
    Season::create(["name" => 'Dia de muertos', 'alias' => 'ddm', 'initial_date' => '2024-10-24', 'final_date' => '2024-11-08']);
    Season::create(["name" => 'Dia de la independencia', 'alias' => '16 sep', 'initial_date' => '2024-09-09', 'final_date' => '2024-09-23']);
    Season::create(["name" => 'Verano', 'alias' => 'verano', 'initial_date' => '2024-06-01', 'final_date' => '2024-07-31']);
    Season::create(["name" => 'Semana santa', 'alias' => 'semana santa', 'initial_date' => '2024-03-20', 'final_date' => '2024-04-15']);
    Season::create(["name" => 'Dia del amor y la amistad', 'alias' => 'amor y amistad', 'initial_date' => '2024-02-05', 'final_date' => '2024-02-20']);
    Season::create(["name" => 'Ano nuevo', 'alias' => 'new year', 'initial_date' => '2024-01-01', 'final_date' => '2024-01-06']);


    // 9 rooms
    /* $rooms = ['Suite 1', 'Suite 2', 'Suite 3', 'Suite 4', 'Suite 5', 'Suite 6', 'Suite 7', 'Suite 8', 'Suite 9'];

    foreach ($rooms as $room) {
      Room::create([
        'name' => $room,
        'price' => rand(2200, 6500),
        'capacity' => rand(2, 9),
        'beds' => rand(1, 7),
        'description' => 'Cuarto de ejemplo creado con datos aleatorios. '
      ]);
    }

    for ($i = 0; $i < 30; $i++) {
      Price::create([
        'amount' => rand(1700, 12500),
        'room_id' => rand(1, 9),
        'season_id' => rand(1, 7)
      ]);
    } */

  }
}
