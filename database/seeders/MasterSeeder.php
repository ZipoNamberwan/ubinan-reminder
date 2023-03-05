<?php

namespace Database\Seeders;

use App\Models\Bs;
use App\Models\Commodity;
use App\Models\Month;
use App\Models\PeriodSetting;
use App\Models\Profile;
use App\Models\SampleType;
use App\Models\Subdistrict;
use App\Models\User;
use App\Models\Village;
use App\Models\Year;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PeriodSetting::create([
            'monthly_reminder_before_date' => '-3 days',
            'monthly_reminder_interval' => '7 days',
            'harvest_reminder_first_before_date' => '-3 days',
            'harvest_reminder_second_before_date' => '-1 day',
        ]);

        Month::create(['name' => "Januari", 'code' => '01']);
        Month::create(['name' => "Februari", 'code' => '02']);
        Month::create(['name' => "Maret", 'code' => '03']);
        Month::create(['name' => "April", 'code' => '04']);
        Month::create(['name' => "Mei", 'code' => '05']);
        Month::create(['name' => "Juni", 'code' => '06']);
        Month::create(['name' => "Juli", 'code' => '07']);
        Month::create(['name' => "Agustus", 'code' => '08']);
        Month::create(['name' => "September", 'code' => '09']);
        Month::create(['name' => "Oktober", 'code' => '10']);
        Month::create(['name' => "November", 'code' => '11']);
        Month::create(['name' => "Desember", 'code' => '12']);

        Year::create(['name' => '2023']);
        Year::create(['name' => '2024']);

        Commodity::create(['name' => 'Jagung']);
        Commodity::create(['name' => 'Ubi Kayu']);
        Commodity::create(['name' => 'Ubi Jalar']);
        Commodity::create(['name' => 'Kacang Hijau']);

        Role::create(['name' => 'supervisor']);
        Role::create(['name' => 'member']);
        Role::create(['name' => 'admin']);

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('123456')
        ]);
        $admin->assignRole('admin');

        Profile::create([
            'phone_number' => '82236981385',
            'user_id' => $admin->id
        ]);

        $sp = User::create([
            'name' => 'supervisor1',
            'email' => 'sp1@gmail.com',
            'password' => bcrypt('123456')
        ]);
        $sp->assignRole('supervisor');

        Profile::create([
            'phone_number' => '82236981385',
            'user_id' => $sp->id
        ]);

        $user1 = User::create([
            'name' => 'user1',
            'email' => 'user1@gmail.com',
            'password' => bcrypt('123456')
        ]);
        $user1->assignRole('member');

        Profile::create([
            'phone_number' => '82236981385',
            'user_id' => $user1->id,
            'supervisor_id' => $sp->id
        ]);

        $user2 = User::create([
            'name' => 'user2',
            'email' => 'user2@gmail.com',
            'password' => bcrypt('123456')
        ]);
        $user2->assignRole('member');

        Profile::create([
            'phone_number' => '82236981385',
            'user_id' => $user2->id,
            'supervisor_id' => $sp->id
        ]);

        $subdistrict =  Subdistrict::create(
            ['name' => 'Sumber', 'code' => '020']
        );

        $village = Village::create(
            ['name' => 'Sumber', 'code' => '020009', 'subdistrict_id' => $subdistrict->id]
        );

        Bs::create(
            ['name' => 'Sumber', 'code' => '020009004B', 'village_id' => $village->id]
        );

        SampleType::create(['name' => 'Utama']);
        SampleType::create(['name' => 'Cadangan']);
    }
}
