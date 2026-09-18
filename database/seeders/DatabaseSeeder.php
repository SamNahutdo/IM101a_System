<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\AthleteProfile;
use App\Models\CoachProfile;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\EquipmentCategory;
use App\Models\EquipmentLocation;
use App\Models\Equipment;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\BorrowingTransaction;
use App\Models\BorrowingItem;
use App\Models\DamageReport;
use App\Models\MaintenanceRecord;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'System Administrator',
            'description' => 'Unrestricted access to all system modules, configurations, and audit records.',
        ]);

        $staffRole = Role::create([
            'name' => 'staff',
            'display_name' => 'Equipment Staff',
            'description' => 'Manages operational equipment transactions, checkouts, returns, inspections, and maintenance.',
        ]);

        $coachRole = Role::create([
            'name' => 'coach',
            'display_name' => 'Coach',
            'description' => 'Manages team roster, team equipment reservations, athlete assignments, and condition tracking.',
        ]);

        $athleteRole = Role::create([
            'name' => 'athlete',
            'display_name' => 'Athlete',
            'description' => 'Browses equipment catalog, submits individual borrowing requests, and reports damaged gear.',
        ]);

        $defaultPassword = Hash::make('password123');

        // 2. Administrator
        $adminUser = User::create([
            'username' => 'admin',
            'email' => 'admin@falconsystem.edu',
            'password' => $defaultPassword,
            'first_name' => 'Arthur',
            'last_name' => 'Vance',
            'phone' => '+63 917 111 0001',
            'is_active' => true,
        ]);
        $adminUser->roles()->attach($adminRole->id, ['is_primary' => true]);

        // 3. Equipment Staff
        $staff1 = User::create([
            'username' => 'staff1',
            'email' => 'marcus.staff@falconsystem.edu',
            'password' => $defaultPassword,
            'first_name' => 'Marcus',
            'last_name' => 'Gomez',
            'phone' => '+63 917 222 0002',
            'is_active' => true,
        ]);
        $staff1->roles()->attach($staffRole->id, ['is_primary' => true]);

        $staff2 = User::create([
            'username' => 'staff2',
            'email' => 'elena.staff@falconsystem.edu',
            'password' => $defaultPassword,
            'first_name' => 'Elena',
            'last_name' => 'Reyes',
            'phone' => '+63 917 222 0003',
            'is_active' => true,
        ]);
        $staff2->roles()->attach($staffRole->id, ['is_primary' => true]);

        // 4. Coaches
        $coachUser1 = User::create([
            'username' => 'coach_carter',
            'email' => 'ken.carter@falconsystem.edu',
            'password' => $defaultPassword,
            'first_name' => 'Ken',
            'last_name' => 'Carter',
            'phone' => '+63 917 333 0001',
            'is_active' => true,
        ]);
        $coachUser1->roles()->attach($coachRole->id, ['is_primary' => true]);
        $coachProfile1 = CoachProfile::create([
            'user_id' => $coachUser1->id,
            'employee_id' => 'EMP-CCH-101',
            'specialization' => 'Basketball & Strength Conditioning',
            'department' => 'Athletics & Physical Education',
            'license_number' => 'FIBA-PH-8891',
        ]);

        $coachUser2 = User::create([
            'username' => 'coach_miller',
            'email' => 'sarah.miller@falconsystem.edu',
            'password' => $defaultPassword,
            'first_name' => 'Sarah',
            'last_name' => 'Miller',
            'phone' => '+63 917 333 0002',
            'is_active' => true,
        ]);
        $coachUser2->roles()->attach($coachRole->id, ['is_primary' => true]);
        $coachProfile2 = CoachProfile::create([
            'user_id' => $coachUser2->id,
            'employee_id' => 'EMP-CCH-102',
            'specialization' => 'Volleyball & Tactical Strategy',
            'department' => 'Athletics & Physical Education',
            'license_number' => 'FIVB-PH-3342',
        ]);

        // 5. Teams
        $teamBball = Team::create([
            'name' => 'Falcon Varsity Men Basketball',
            'sport' => 'Basketball',
            'coach_id' => $coachProfile1->id,
            'gender_category' => 'Men',
            'status' => 'Active',
        ]);

        $teamVball = Team::create([
            'name' => 'Falcon Lady Spikers Volleyball',
            'sport' => 'Volleyball',
            'coach_id' => $coachProfile2->id,
            'gender_category' => 'Women',
            'status' => 'Active',
        ]);

        $teamBaseball = Team::create([
            'name' => 'Falcon Hardball Baseball Team',
            'sport' => 'Baseball',
            'coach_id' => $coachProfile1->id,
            'gender_category' => 'Men',
            'status' => 'Active',
        ]);

        // 6. Athletes & Profiles
        $athletesData = [
            ['username' => 'athlete1', 'first' => 'James', 'last' => 'Navarro', 'stud_id' => '2024-00101', 'year' => 3, 'pos' => 'Point Guard', 'no' => '7', 'team' => $teamBball],
            ['username' => 'athlete2', 'first' => 'Carlos', 'last' => 'Santos', 'stud_id' => '2024-00102', 'year' => 2, 'pos' => 'Shooting Guard', 'no' => '13', 'team' => $teamBball],
            ['username' => 'athlete3', 'first' => 'David', 'last' => 'Lim', 'stud_id' => '2023-00103', 'year' => 4, 'pos' => 'Center', 'no' => '34', 'team' => $teamBball],
            ['username' => 'athlete4', 'first' => 'Jasmine', 'last' => 'Aquino', 'stud_id' => '2024-00201', 'year' => 2, 'pos' => 'Outside Hitter', 'no' => '10', 'team' => $teamVball],
            ['username' => 'athlete5', 'first' => 'Alyssa', 'last' => 'Valdez', 'stud_id' => '2023-00202', 'year' => 4, 'pos' => 'Setter', 'no' => '2', 'team' => $teamVball],
            ['username' => 'athlete6', 'first' => 'Bea', 'last' => 'De Leon', 'stud_id' => '2025-00203', 'year' => 1, 'pos' => 'Middle Blocker', 'no' => '14', 'team' => $teamVball],
            ['username' => 'athlete7', 'first' => 'Christian', 'last' => 'Perez', 'stud_id' => '2024-00301', 'year' => 3, 'pos' => 'Pitcher', 'no' => '18', 'team' => $teamBaseball],
            ['username' => 'athlete8', 'first' => 'Mateo', 'last' => 'Cruz', 'stud_id' => '2025-00302', 'year' => 1, 'pos' => 'Catcher', 'no' => '24', 'team' => $teamBaseball],
            ['username' => 'athlete9', 'first' => 'Daniel', 'last' => 'Tan', 'stud_id' => '2024-00104', 'year' => 3, 'pos' => 'Power Forward', 'no' => '21', 'team' => $teamBball],
            ['username' => 'athlete10', 'first' => 'Chloe', 'last' => 'Mercado', 'stud_id' => '2025-00204', 'year' => 1, 'pos' => 'Libero', 'no' => '5', 'team' => $teamVball],
        ];

        $athleteProfiles = [];
        $athleteUsers = [];
        foreach ($athletesData as $ad) {
            $user = User::create([
                'username' => $ad['username'],
                'email' => "{$ad['username']}@falconsystem.edu",
                'password' => $defaultPassword,
                'first_name' => $ad['first'],
                'last_name' => $ad['last'],
                'phone' => '+63 918 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                'is_active' => true,
            ]);
            $user->roles()->attach($athleteRole->id, ['is_primary' => true]);

            $prof = AthleteProfile::create([
                'user_id' => $user->id,
                'student_id' => $ad['stud_id'],
                'emergency_contact_name' => "Parent of {$ad['first']}",
                'emergency_contact_phone' => '+63 919 555 ' . rand(1000, 9999),
                'medical_clearance_status' => 'Cleared',
                'year_level' => $ad['year'],
            ]);

            TeamMember::create([
                'team_id' => $ad['team']->id,
                'athlete_id' => $prof->id,
                'jersey_number' => $ad['no'],
                'position' => $ad['pos'],
                'membership_status' => 'Active',
                'joined_at' => Carbon::now()->subMonths(6),
            ]);

            $athleteProfiles[] = $prof;
            $athleteUsers[] = $user;
        }

        // 7. Categories
        $catBball = EquipmentCategory::create(['name' => 'Basketball Equipment', 'code' => 'BBALL', 'description' => 'Balls, hoops accessories, training boards, and shot clocks']);
        $catVball = EquipmentCategory::create(['name' => 'Volleyball Equipment', 'code' => 'VBALL', 'description' => 'Volleyballs, tournament nets, antenna rods, and carts']);
        $catBase = EquipmentCategory::create(['name' => 'Baseball Equipment', 'code' => 'BASE', 'description' => 'Bats, catchers gear, gloves, and batting helmets']);
        $catProt = EquipmentCategory::create(['name' => 'Protective & Fitness Gear', 'code' => 'PROT', 'description' => 'Knee pads, training cones, agility ladders, and weight belts']);

        // 8. Locations
        $locGymA = EquipmentLocation::create(['building' => 'Main Gymnasium', 'room' => 'Equipment Vault A', 'shelf_bin' => 'Rack 1-A', 'description' => 'Main sports ball inventory']);
        $locGymB = EquipmentLocation::create(['building' => 'Main Gymnasium', 'room' => 'Equipment Vault A', 'shelf_bin' => 'Bin 3-C', 'description' => 'Nets and bulky gear']);
        $locField = EquipmentLocation::create(['building' => 'Athletic Fieldhouse', 'room' => 'Field Storage 102', 'shelf_bin' => 'Locker B-4', 'description' => 'Baseball and outdoor equipment']);

        // 9. Equipment Units
        $eqData = [
            ['name' => 'Spalding TF-1000 Legacy Official Basketball', 'code' => 'EQ-BBALL-001', 'cat' => $catBball, 'loc' => $locGymA, 'cost' => 4500.00, 'cond' => 'Excellent', 'stat' => 'Available', 'sn' => 'SPL-9901'],
            ['name' => 'Spalding TF-1000 Legacy Official Basketball', 'code' => 'EQ-BBALL-002', 'cat' => $catBball, 'loc' => $locGymA, 'cost' => 4500.00, 'cond' => 'Good', 'stat' => 'Available', 'sn' => 'SPL-9902'],
            ['name' => 'Molten B7G5000 FIBA Official Game Ball', 'code' => 'EQ-BBALL-003', 'cat' => $catBball, 'loc' => $locGymA, 'cost' => 5800.00, 'cond' => 'Excellent', 'stat' => 'Borrowed', 'sn' => 'MLT-7703'],
            ['name' => 'Molten V5M5000 NCAA Official Volleyball', 'code' => 'EQ-VBALL-001', 'cat' => $catVball, 'loc' => $locGymA, 'cost' => 3900.00, 'cond' => 'Excellent', 'stat' => 'Available', 'sn' => 'MLT-V001'],
            ['name' => 'Molten V5M5000 NCAA Official Volleyball', 'code' => 'EQ-VBALL-002', 'cat' => $catVball, 'loc' => $locGymA, 'cost' => 3900.00, 'cond' => 'Good', 'stat' => 'Available', 'sn' => 'MLT-V002'],
            ['name' => 'Mikasa V200W Olympic Match Volleyball', 'code' => 'EQ-VBALL-003', 'cat' => $catVball, 'loc' => $locGymA, 'cost' => 4800.00, 'cond' => 'Fair', 'stat' => 'Maintenance', 'sn' => 'MKS-V200'],
            ['name' => 'Senoh Championship Volleyball Net & Antennas', 'code' => 'EQ-VBALL-004', 'cat' => $catVball, 'loc' => $locGymB, 'cost' => 18500.00, 'cond' => 'Good', 'stat' => 'Available', 'sn' => 'SNH-NET-01'],
            ['name' => 'Easton Maxum 360 BBCOR Baseball Bat (-3)', 'code' => 'EQ-BASE-001', 'cat' => $catBase, 'loc' => $locField, 'cost' => 14200.00, 'cond' => 'Good', 'stat' => 'Borrowed', 'sn' => 'EST-BAT-01'],
            ['name' => 'Rawlings Heart of the Hide Fielding Glove', 'code' => 'EQ-BASE-002', 'cat' => $catBase, 'loc' => $locField, 'cost' => 12500.00, 'cond' => 'Excellent', 'stat' => 'Available', 'sn' => 'RWL-GLV-01'],
            ['name' => 'Mizuno Samurai Pro Catchers Gear Set', 'code' => 'EQ-BASE-003', 'cat' => $catBase, 'loc' => $locField, 'cost' => 22000.00, 'cond' => 'Fair', 'stat' => 'Damaged', 'sn' => 'MZN-CAT-01'],
            ['name' => 'SKLZ Pro Training Agility Cones Set (20pcs)', 'code' => 'EQ-PROT-001', 'cat' => $catProt, 'loc' => $locGymB, 'cost' => 2800.00, 'cond' => 'Good', 'stat' => 'Available', 'sn' => 'SKL-CONE-01'],
            ['name' => 'Pro Agility Speed Ladder 15ft', 'code' => 'EQ-PROT-002', 'cat' => $catProt, 'loc' => $locGymB, 'cost' => 1950.00, 'cond' => 'Excellent', 'stat' => 'Available', 'sn' => 'LDR-15-01'],
            ['name' => 'McDavid Hex Knee Protective Pads (Pair)', 'code' => 'EQ-PROT-003', 'cat' => $catProt, 'loc' => $locGymB, 'cost' => 2400.00, 'cond' => 'Good', 'stat' => 'Assigned', 'sn' => 'MCD-PAD-01'],
        ];

        $eqModels = [];
        foreach ($eqData as $ed) {
            $eq = Equipment::create([
                'name' => $ed['name'],
                'asset_code' => $ed['code'],
                'category_id' => $ed['cat']->id,
                'location_id' => $ed['loc']->id,
                'serial_number' => $ed['sn'],
                'purchase_date' => Carbon::now()->subMonths(8),
                'purchase_cost' => $ed['cost'],
                'current_condition' => $ed['cond'],
                'status' => $ed['stat'],
                'description' => 'Official varsity competition gear. Certified for collegiate athletics.',
            ]);
            $eqModels[$ed['code']] = $eq;
        }

        // 10. Sample Reservation: Coach Carter requests basketballs for scrimmage
        $res1 = Reservation::create([
            'reservation_code' => 'RES-2026-001',
            'requester_id' => $coachUser1->id,
            'team_id' => $teamBball->id,
            'start_time' => Carbon::now()->addDays(2)->setHour(14)->setMinute(0),
            'end_time' => Carbon::now()->addDays(2)->setHour(18)->setMinute(0),
            'purpose' => 'Falcon Varsity Championship preparation scrimmage vs visiting team.',
            'status' => 'Approved',
            'reviewed_by' => $staff1->id,
            'reviewed_at' => Carbon::now()->subHours(3),
            'review_notes' => 'Approved. Please pick up 30 minutes before tip-off.',
        ]);
        ReservationItem::create([
            'reservation_id' => $res1->id,
            'equipment_id' => $eqModels['EQ-BBALL-001']->id,
            'requested_quantity' => 1,
            'approved_quantity' => 1,
            'item_status' => 'Approved',
            'notes' => 'Game ball requested.',
        ]);

        // 11. Sample Active Borrowing: Athlete Carlos Santos borrowed Molten Basketball
        $trans1 = BorrowingTransaction::create([
            'transaction_code' => 'BOR-2026-001',
            'reservation_id' => null,
            'borrower_id' => $athleteUsers[1]->id, // Carlos Santos
            'team_id' => $teamBball->id,
            'processed_by' => $staff1->id,
            'checkout_time' => Carbon::now()->subHours(4),
            'expected_return_time' => Carbon::now()->addHours(2),
            'status' => 'Active',
            'notes' => 'Individual free throw shooting drills.',
        ]);
        BorrowingItem::create([
            'transaction_id' => $trans1->id,
            'equipment_id' => $eqModels['EQ-BBALL-003']->id,
            'checkout_condition' => 'Excellent',
            'status' => 'Borrowed',
            'remarks' => 'Clean checkout, standard pressure verified.',
        ]);

        // 12. Sample Overdue Borrowing: Athlete Christian Perez borrowed Baseball bat 3 days ago
        $transOverdue = BorrowingTransaction::create([
            'transaction_code' => 'BOR-2026-002',
            'reservation_id' => null,
            'borrower_id' => $athleteUsers[6]->id, // Christian Perez
            'team_id' => $teamBaseball->id,
            'processed_by' => $staff2->id,
            'checkout_time' => Carbon::now()->subDays(3),
            'expected_return_time' => Carbon::now()->subDay(),
            'actual_return_time' => null,
            'status' => 'Active',
            'notes' => 'Weekend batting cage practice.',
        ]);
        BorrowingItem::create([
            'transaction_id' => $transOverdue->id,
            'equipment_id' => $eqModels['EQ-BASE-001']->id,
            'checkout_condition' => 'Good',
            'status' => 'Borrowed',
            'remarks' => 'Standard bat grip tape applied.',
        ]);

        // 13. Sample Damage Report & Maintenance: Catchers Gear Set
        $dmg = DamageReport::create([
            'report_code' => 'DMG-2026-001',
            'equipment_id' => $eqModels['EQ-BASE-003']->id,
            'reported_by' => $coachUser1->id,
            'damage_type' => 'Cracked Chest Protector Buckle',
            'severity' => 'Severe',
            'description' => 'Right retention buckle snapped during sliding drill at home plate. Cannot strap safely.',
            'status' => 'Repair Required',
            'reported_at' => Carbon::now()->subDays(1),
        ]);

        MaintenanceRecord::create([
            'equipment_id' => $eqModels['EQ-BASE-003']->id,
            'damage_report_id' => $dmg->id,
            'maintenance_type' => 'Buckle & Strap Replacement',
            'scheduled_date' => Carbon::now()->addDay(),
            'assigned_staff_id' => $staff1->id,
            'cost' => 850.00,
            'status' => 'Scheduled',
            'description' => 'Order OEM replacement nylon strap and heavy-duty steel buckles.',
        ]);

        // Volleyball in maintenance
        MaintenanceRecord::create([
            'equipment_id' => $eqModels['EQ-VBALL-003']->id,
            'damage_report_id' => null,
            'maintenance_type' => 'Valve Core Reseal & Bladder Inspection',
            'scheduled_date' => Carbon::now(),
            'start_date' => Carbon::now(),
            'assigned_staff_id' => $staff2->id,
            'cost' => 300.00,
            'status' => 'In Progress',
            'description' => 'Slow air leak around inflation valve detected during practice.',
        ]);
    }
}
