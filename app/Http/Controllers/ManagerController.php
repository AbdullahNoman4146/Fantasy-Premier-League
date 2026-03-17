<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    public function index()
    {
        $managers = collect(DB::select("
            SELECT
                m.person_id,
                m.team_id,
                m.experience_years,
                p.first_name,
                p.last_name,
                p.nationality,
                t.team_name
            FROM managers m
            JOIN persons p ON p.person_id = m.person_id
            LEFT JOIN teams t ON t.team_id = m.team_id
            ORDER BY t.team_name ASC, p.first_name ASC, p.last_name ASC
        "));

        return view('managers', compact('managers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'team_id' => ['required', 'integer', 'exists:teams,team_id'],
        ]);

        DB::transaction(function () use ($request) {
            $this->detachCurrentManagerFromTeam((int) $request->team_id);

            $personId = DB::table('persons')->insertGetId([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'nationality' => $request->nationality,
                'attribute' => 'manager',
            ]);

            DB::table('managers')->insert([
                'person_id' => $personId,
                'team_id' => $request->team_id,
                'experience_years' => $request->experience_years,
            ]);

            DB::table('teams')
                ->where('team_id', $request->team_id)
                ->update(['manager_id' => $personId]);
        });

        return redirect()->route('admin.panel')->with('success', 'Manager assigned successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'person_id' => ['required', 'integer', 'exists:managers,person_id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'team_id' => ['nullable', 'integer', 'exists:teams,team_id'],
        ]);

        DB::transaction(function () use ($request) {
            $currentManager = DB::table('managers')->where('person_id', $request->person_id)->first();

            if ($currentManager && $currentManager->team_id && (int) $currentManager->team_id !== (int) $request->team_id) {
                DB::table('teams')->where('team_id', $currentManager->team_id)->update(['manager_id' => null]);
            }

            if ($request->filled('team_id')) {
                $this->detachCurrentManagerFromTeam((int) $request->team_id, (int) $request->person_id);
                DB::table('teams')->where('team_id', $request->team_id)->update(['manager_id' => $request->person_id]);
            }

            DB::table('persons')
                ->where('person_id', $request->person_id)
                ->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'nationality' => $request->nationality,
                    'attribute' => 'manager',
                ]);

            DB::table('managers')
                ->where('person_id', $request->person_id)
                ->update([
                    'team_id' => $request->team_id,
                    'experience_years' => $request->experience_years,
                ]);
        });

        return redirect()->route('admin.panel')->with('success', 'Manager updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'person_id' => ['required', 'integer', 'exists:managers,person_id'],
        ]);

        DB::transaction(function () use ($request) {
            $manager = DB::table('managers')->where('person_id', $request->person_id)->first();

            if ($manager && $manager->team_id) {
                DB::table('teams')->where('team_id', $manager->team_id)->update(['manager_id' => null]);
            }

            DB::delete('DELETE FROM managers WHERE person_id = ?', [$request->person_id]);
            DB::delete('DELETE FROM persons WHERE person_id = ?', [$request->person_id]);
        });

        return redirect()->route('admin.panel')->with('success', 'Manager deleted successfully.');
    }

    private function detachCurrentManagerFromTeam(int $teamId, ?int $ignorePersonId = null): void
    {
        $current = DB::table('managers')->where('team_id', $teamId)->first();

        if (!$current) {
            return;
        }

        if ($ignorePersonId !== null && (int) $current->person_id === $ignorePersonId) {
            return;
        }

        DB::table('teams')->where('team_id', $teamId)->update(['manager_id' => null]);
        DB::table('managers')->where('person_id', $current->person_id)->update(['team_id' => null]);
    }
}