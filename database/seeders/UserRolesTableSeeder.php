<?php

namespace Database\Seeders;

use App\Http\Controllers\Api\Dashboard\StatsController as DashboardStatsController;
use App\Models\UserRole;
use Exception;
use Illuminate\Database\Seeder;

class UserRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        if (UserRole::count() === 0) {
            // Add Admin Role
            $userRole = new UserRole();
            $userRole->name = 'Admin';
            $userRole->type = 1;
            $userRole->permissions = '[]';
            $userRole->dashboard_access = 1;
            $userRole->save();
            // Add Department Role
            $userRole = new UserRole();
            $userRole->name = 'Department';
            $userRole->type = 2;
            $userRole->permissions = json_encode([str_replace('\\', '.', DashboardStatsController::class)], JSON_THROW_ON_ERROR);
            $userRole->dashboard_access = 1;
            $userRole->save();
            // Add Technician Role
            $userRole = new UserRole();
            $userRole->name = 'Technician';
            $userRole->type = 2;
            $userRole->permissions = json_encode([str_replace('\\', '.', DashboardStatsController::class)], JSON_THROW_ON_ERROR);
            $userRole->dashboard_access = 1;
            $userRole->save();
        }
    }
}
