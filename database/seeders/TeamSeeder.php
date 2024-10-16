<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\User;

class TeamSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'System Admin',
            'email' => 'systemadmin@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'system_admin',
        ]);

        $aTeams = [
            [
                'team_name' => 'Team 1',
                'admin' => [
                    'name' => 'Team 1 Admin',
                    'email' => 'team1admin@gmail.com',
                    'role' => 'team_admin',
                ],
                'member' => [
                    'name' => 'Team 1 Member',
                    'email' => 'team1member@gmail.com',
                    'role' => 'team_member',
                ],
            ],
            [
                'team_name' => 'Team 2',
                'admin' => [
                    'name' => 'Team 2 Admin',
                    'email' => 'team2admin@gmail.com',
                    'role' => 'team_admin',
                ],
                'member' => [
                    'name' => 'Team 2 Member',
                    'email' => 'team2member@gmail.com',
                    'role' => 'team_member',
                ],
            ]
        ];

        foreach ($aTeams as $aTeam) {
            $team = Team::create(['name' => $aTeam['team_name']]);
            $teamAdmin = User::create([
                'name' => $aTeam['admin']['name'],
                'email' => $aTeam['admin']['email'],
                'password' => bcrypt('12345678'), // Default password
                'role' => $aTeam['admin']['role'],
            ]);
            $teamAdmin->teams()->attach($team->id);

            $teamMember = User::create([
                'name' => $aTeam['member']['name'],
                'email' => $aTeam['member']['email'],
                'password' => bcrypt('12345678'),
                'role' => $aTeam['member']['role'],
            ]);
            $teamMember->teams()->attach($team->id);
        }
    }
}
