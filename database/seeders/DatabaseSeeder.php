<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Sport;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Equipment;
use App\Models\BorrowingTransaction;
use App\Models\BorrowingItem;
use App\Models\MaintenanceRecord;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Athletics Department Administrator']);
        $staffRole = Role::firstOrCreate(['name' => 'staff'], ['description' => 'Equipment Custodian & Staff']);
        $coachRole = Role::firstOrCreate(['name' => 'coach'], ['description' => 'Varsity & Club Team Coach']);
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['description' => 'Student Athlete']);

        // 2. Users & Profiles
        // Admin: Ma'am Arbe
        $adminUser = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'role_id' => $adminRole->id,
                'email' => 'arbe@falcons.edu.ph',
                'password' => Hash::make('password123'),
                'status' => 'Active',
            ]
        );

        // Staff: Custodian John
        $staffUser = User::firstOrCreate(
            ['username' => 'staff1'],
            [
                'role_id' => $staffRole->id,
                'email' => 'custodian@falcons.edu.ph',
                'password' => Hash::make('password123'),
                'status' => 'Active',
            ]
        );

        // Coaches
        $coach1User = User::firstOrCreate(
            ['username' => 'coach_baseball'],
            [
                'role_id' => $coachRole->id,
                'email' => 'michael.reyes@falcons.edu.ph',
                'password' => Hash::make('password123'),
                'status' => 'Active',
            ]
        );
        $coach1 = Coach::firstOrCreate(
            ['user_id' => $coach1User->id],
            [
                'employee_number' => 'EMP-2021-001',
                'first_name' => 'Michael',
                'last_name' => 'Reyes',
                'contact_number' => '09171112222',
                'status' => 'Active',
            ]
        );

        $coach2User = User::firstOrCreate(
            ['username' => 'coach_softball'],
            [
                'role_id' => $coachRole->id,
                'email' => 'maria.santos@falcons.edu.ph',
                'password' => Hash::make('password123'),
                'status' => 'Active',
            ]
        );
        $coach2 = Coach::firstOrCreate(
            ['user_id' => $coach2User->id],
            [
                'employee_number' => 'EMP-2022-002',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'contact_number' => '09183334444',
                'status' => 'Active',
            ]
        );

        $coach3User = User::firstOrCreate(
            ['username' => 'coach_tabletennis'],
            [
                'role_id' => $coachRole->id,
                'email' => 'david.lim@falcons.edu.ph',
                'password' => Hash::make('password123'),
                'status' => 'Active',
            ]
        );
        $coach3 = Coach::firstOrCreate(
            ['user_id' => $coach3User->id],
            [
                'employee_number' => 'EMP-2023-003',
                'first_name' => 'David',
                'last_name' => 'Lim',
                'contact_number' => '09195556666',
                'status' => 'Active',
            ]
        );

        // Athletes
        $athlete1User = User::firstOrCreate(
            ['username' => 'athlete_juan'],
            [
                'role_id' => $studentRole->id,
                'email' => 'juan.delacruz@falcons.edu.ph',
                'password' => Hash::make('password123'),
                'status' => 'Active',
            ]
        );
        $athlete1 = Athlete::firstOrCreate(
            ['user_id' => $athlete1User->id],
            [
                'student_number' => '2024-00101',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'department' => 'College of Computer Studies',
                'year_level' => 3,
                'contact_number' => '09201110001',
                'status' => 'Active',
            ]
        );

        $athlete2User = User::firstOrCreate(
            ['username' => 'athlete_ana'],
            [
                'role_id' => $studentRole->id,
                'email' => 'ana.gonzales@falcons.edu.ph',
                'password' => Hash::make('password123'),
                'status' => 'Active',
            ]
        );
        $athlete2 = Athlete::firstOrCreate(
            ['user_id' => $athlete2User->id],
            [
                'student_number' => '2025-00202',
                'first_name' => 'Ana',
                'last_name' => 'Gonzales',
                'department' => 'College of Engineering',
                'year_level' => 2,
                'contact_number' => '09201110002',
                'status' => 'Active',
            ]
        );

        $athlete3User = User::firstOrCreate(
            ['username' => 'athlete_mark'],
            [
                'role_id' => $studentRole->id,
                'email' => 'mark.bautista@falcons.edu.ph',
                'password' => Hash::make('password123'),
                'status' => 'Active',
            ]
        );
        $athlete3 = Athlete::firstOrCreate(
            ['user_id' => $athlete3User->id],
            [
                'student_number' => '2024-00303',
                'first_name' => 'Mark',
                'last_name' => 'Bautista',
                'department' => 'College of Business Administration',
                'year_level' => 3,
                'contact_number' => '09201110003',
                'status' => 'Active',
            ]
        );

        // 3. Sports
        $baseball = Sport::firstOrCreate(['sport_name' => 'Baseball'], ['description' => 'Collegiate Baseball Division', 'status' => 'Active']);
        $softball = Sport::firstOrCreate(['sport_name' => 'Softball'], ['description' => 'Collegiate Women Softball Division', 'status' => 'Active']);
        $tableTennis = Sport::firstOrCreate(['sport_name' => 'Table Tennis'], ['description' => 'Men and Women Table Tennis Club', 'status' => 'Active']);

        // 4. Teams
        $baseballTeam = Team::firstOrCreate(
            ['team_name' => 'Falcons Men Varsity Baseball'],
            [
                'sport_id' => $baseball->id,
                'coach_id' => $coach1->id,
                'school_year' => '2026-2027',
                'status' => 'Active',
            ]
        );

        $softballTeam = Team::firstOrCreate(
            ['team_name' => 'Lady Falcons Varsity Softball'],
            [
                'sport_id' => $softball->id,
                'coach_id' => $coach2->id,
                'school_year' => '2026-2027',
                'status' => 'Active',
            ]
        );

        $ttTeam = Team::firstOrCreate(
            ['team_name' => 'Falcons Table Tennis Team'],
            [
                'sport_id' => $tableTennis->id,
                'coach_id' => $coach3->id,
                'school_year' => '2026-2027',
                'status' => 'Active',
            ]
        );

        // Many-to-Many 1: Team Members
        TeamMember::firstOrCreate(
            ['team_id' => $baseballTeam->id, 'athlete_id' => $athlete1->id],
            ['joined_at' => '2024-08-15', 'position' => 'Shortstop', 'status' => 'Active']
        );
        TeamMember::firstOrCreate(
            ['team_id' => $baseballTeam->id, 'athlete_id' => $athlete3->id],
            ['joined_at' => '2024-08-15', 'position' => 'Pitcher', 'status' => 'Active']
        );
        TeamMember::firstOrCreate(
            ['team_id' => $softballTeam->id, 'athlete_id' => $athlete2->id],
            ['joined_at' => '2025-01-10', 'position' => 'Catcher', 'status' => 'Active']
        );
        TeamMember::firstOrCreate(
            ['team_id' => $ttTeam->id, 'athlete_id' => $athlete1->id],
            ['joined_at' => '2025-02-01', 'position' => 'Singles Player', 'status' => 'Active']
        );

        // 5. Equipment Inventory
        $bat = Equipment::firstOrCreate(
            ['equipment_code' => 'EQ-BB-001'],
            [
                'sport_id' => $baseball->id,
                'equipment_name' => 'Rawlings Alloy Baseball Bat 33-inch',
                'category' => 'Bats',
                'quantity' => 10,
                'available_quantity' => 8,
                'condition' => 'Good',
                'status' => 'Available',
            ]
        );

        $glove = Equipment::firstOrCreate(
            ['equipment_code' => 'EQ-BB-002'],
            [
                'sport_id' => $baseball->id,
                'equipment_name' => 'Wilson A2000 Infield Baseball Glove',
                'category' => 'Gloves',
                'quantity' => 12,
                'available_quantity' => 11,
                'condition' => 'Good',
                'status' => 'Available',
            ]
        );

        $balls = Equipment::firstOrCreate(
            ['equipment_code' => 'EQ-BB-003'],
            [
                'sport_id' => $baseball->id,
                'equipment_name' => 'Diamond Official League Baseballs (Dozen)',
                'category' => 'Balls',
                'quantity' => 20,
                'available_quantity' => 20,
                'condition' => 'New',
                'status' => 'Available',
            ]
        );

        $softballBat = Equipment::firstOrCreate(
            ['equipment_code' => 'EQ-SB-001'],
            [
                'sport_id' => $softball->id,
                'equipment_name' => 'DeMarini CF Fastpitch Softball Bat',
                'category' => 'Bats',
                'quantity' => 8,
                'available_quantity' => 8,
                'condition' => 'Good',
                'status' => 'Available',
            ]
        );

        $ttPaddle = Equipment::firstOrCreate(
            ['equipment_code' => 'EQ-TT-001'],
            [
                'sport_id' => $tableTennis->id,
                'equipment_name' => 'Butterfly Timo Boll ALC Table Tennis Paddle',
                'category' => 'Paddles',
                'quantity' => 14,
                'available_quantity' => 14,
                'condition' => 'Good',
                'status' => 'Available',
            ]
        );

        $ttBalls = Equipment::firstOrCreate(
            ['equipment_code' => 'EQ-TT-002'],
            [
                'sport_id' => $tableTennis->id,
                'equipment_name' => 'Nittaku 3-Star Premium 40+ Plastic Balls (Box)',
                'category' => 'Balls',
                'quantity' => 25,
                'available_quantity' => 25,
                'condition' => 'New',
                'status' => 'Available',
            ]
        );

        // 6. Sample Borrowing Transactions & Items (Many-to-Many 2)
        // Transaction 1: Active Loan (Juan Dela Cruz borrowed 2 bats)
        $tx1 = BorrowingTransaction::firstOrCreate(
            ['athlete_id' => $athlete1->id, 'borrow_date' => now()->subDays(2)->toDateString()],
            [
                'approved_by' => $adminUser->id,
                'expected_return_date' => now()->addDays(3)->toDateString(),
                'status' => 'Borrowed',
                'remarks' => 'Varsity batting practice session',
            ]
        );
        BorrowingItem::firstOrCreate(
            ['borrowing_transaction_id' => $tx1->id, 'equipment_id' => $bat->id],
            [
                'quantity' => 2,
                'condition_before' => 'Good',
                'returned_quantity' => 0,
            ]
        );

        // Transaction 2: Overdue Loan (Mark Bautista borrowed 1 glove)
        $tx2 = BorrowingTransaction::firstOrCreate(
            ['athlete_id' => $athlete3->id, 'borrow_date' => now()->subDays(10)->toDateString()],
            [
                'approved_by' => $staffUser->id,
                'expected_return_date' => now()->subDays(3)->toDateString(), // Overdue by 3 days!
                'status' => 'Borrowed',
                'remarks' => 'Defense drills',
            ]
        );
        BorrowingItem::firstOrCreate(
            ['borrowing_transaction_id' => $tx2->id, 'equipment_id' => $glove->id],
            [
                'quantity' => 1,
                'condition_before' => 'Good',
                'returned_quantity' => 0,
            ]
        );

        // 7. Maintenance Records
        MaintenanceRecord::firstOrCreate(
            ['equipment_id' => $bat->id, 'scheduled_date' => now()->addDays(5)->toDateString()],
            [
                'reported_by' => $coach1User->id,
                'maintenance_type' => 'Grip Tape Replacement & Inspection',
                'description' => 'Handle grip tape is peeling on practice bats',
                'status' => 'Scheduled',
                'remarks' => 'Supplies ordered at campus bookstore',
            ]
        );
    }
}
