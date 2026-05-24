<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\Group;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::firstOrCreate(
            ['name' => 'FATI Academy'],
            []
        );

        $branches = [
            'Science Branch',
            'Literature Branch',
        ];

        $createdBranches = [];
        foreach ($branches as $branchName) {
            $createdBranches[$branchName] = Branch::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'name' => $branchName,
                ],
                []
            );
        }

        $currentYear = AcademicYear::firstOrCreate(
            ['name' => '2025 / 2026'],
            ['is_active' => true]
        );

        $previousYear = AcademicYear::firstOrCreate(
            ['name' => '2024 / 2025'],
            ['is_active' => false]
        );

        $groups = [
            ['name' => 'Science A', 'branch' => $createdBranches['Science Branch'], 'year' => $currentYear],
            ['name' => 'Science B', 'branch' => $createdBranches['Science Branch'], 'year' => $currentYear],
            ['name' => 'Literature A', 'branch' => $createdBranches['Literature Branch'], 'year' => $currentYear],
            ['name' => 'Literature Legacy', 'branch' => $createdBranches['Literature Branch'], 'year' => $previousYear],
        ];

        $createdGroups = [];
        foreach ($groups as $groupData) {
            $group = Group::firstOrCreate(
                [
                    'branch_id' => $groupData['branch']->id,
                    'academic_year_id' => $groupData['year']->id,
                    'name' => $groupData['name'],
                ],
                []
            );

            $createdGroups[] = $group;
        }

        $students = [
            [
                'username' => 'MASSAR-SCI-001',
                'first_name' => 'Amina',
                'last_name' => 'Safi',
                'age' => 15,
                'massar_code' => 'MASSAR-SCI-001',
                'group' => $createdGroups[0],
            ],
            [
                'username' => 'MASSAR-SCI-002',
                'first_name' => 'Youssef',
                'last_name' => 'Amrani',
                'age' => 16,
                'massar_code' => 'MASSAR-SCI-002',
                'group' => $createdGroups[1],
            ],
            [
                'username' => 'MASSAR-LIT-001',
                'first_name' => 'Sara',
                'last_name' => 'El Idrissi',
                'age' => 15,
                'massar_code' => 'MASSAR-LIT-001',
                'group' => $createdGroups[2],
            ],
            [
                'username' => 'MASSAR-LIT-002',
                'first_name' => 'Omar',
                'last_name' => 'Bennani',
                'age' => 17,
                'massar_code' => 'MASSAR-LIT-002',
                'group' => $createdGroups[3],
            ],
        ];

        foreach ($students as $student) {
            User::firstOrCreate(
                ['username' => $student['username']],
                [
                    'role' => 'student',
                    'group_id' => $student['group']->id,
                    'first_name' => $student['first_name'],
                    'last_name' => $student['last_name'],
                    'age' => $student['age'],
                    'massar_code' => $student['massar_code'],
                    'password' => Hash::make($student['massar_code']),
                    'is_first_login' => false,
                ]
            );
        }
    }
}
