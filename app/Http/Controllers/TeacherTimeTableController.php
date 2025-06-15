<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\TimeTable;

class TeacherTimeTableController extends Controller
{
    /**
     * Display the teacher's timetable page.
     */
    public function index()
    {
        return inertia('Teacher/timetable', [
            'currentTerm' => $this->getCurrentTerm(),
            'academicYear' => $this->getCurrentAcademicYear(),
            'error' => null
        ]);
    }

    /**
     * Fetch the teacher's timetable.
     */
    public function fetchTimetable(Request $request)
    {
        $teacher = $this->getAuthenticatedTeacher();

        if (!$teacher) {
            return response()->json([
                'error' => 'No teacher record found for your account.',
                'timetable' => null
            ], 400);
        }

        $validated = $request->validate([
            'term' => 'required|in:1st Term,2nd Term,3rd Term',
            'academic_year' => 'required|string'
        ]);

        $timetableEntries = $this->getTimetableEntries(
            $teacher->id,
            $validated['term'],
            $validated['academic_year']
        );

        if ($timetableEntries->isEmpty()) {
            return response()->json([
                'timetable' => null,
                'message' => 'No timetable found for your teaching schedule'
            ], 404);
        }

        $formattedTimetable = $this->formatTeacherTimetable($timetableEntries);

        return response()->json([
            'timetable' => $formattedTimetable,
            'message' => 'Timetable loaded successfully',
            'teacher' => $teacher
        ]);
    }

    /**
     * Get the authenticated teacher.
     */
    private function getAuthenticatedTeacher()
    {
        $user = auth()->user();

        return Teacher::where('user_id', $user->id)->first();
    }

    /**
     * Get the timetable entries for the given teacher, term, and academic year.
     */
    private function getTimetableEntries($teacherId, $term, $academicYear)
    {
        return TimeTable::where('teacher_id', $teacherId)
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->with(['subject:id,name', 'klass:id,class_name'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Format timetable entries into a structured timetable.
     */
    private function formatTeacherTimetable($timetableEntries)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $formatted = [];

        // Initialize structure
        foreach ($days as $day) {
            $formatted[$day] = [];
        }

        // Group entries by day
        foreach ($timetableEntries as $entry) {
            $day = ucfirst(strtolower($entry->day_of_week));

            if (in_array($day, $days)) {
                $formatted[$day][] = [
                    'period_name' => $entry->period_name,
                    'start_time' => $entry->start_time,
                    'end_time' => $entry->end_time,
                    'subject_name' => $entry->subject?->name ?? '',
                    'class_name' => $entry->klass?->class_name ?? '',
                    'is_break' => $entry->is_break ?? false
                ];
            }
        }

        // Sort periods by start time for each day
        foreach ($formatted as $day => $periods) {
            usort($formatted[$day], function ($a, $b) {
                return strcmp($a['start_time'], $b['start_time']);
            });
        }

        return $formatted;
    }

    /**
     * Determine the current term.
     */
    private function getCurrentTerm()
    {
        $currentMonth = date('n');

        if ($currentMonth >= 9 || $currentMonth <= 12) {
            return '1st Term';
        } elseif ($currentMonth >= 1 && $currentMonth <= 4) {
            return '2nd Term';
        } else {
            return '3rd Term';
        }
    }

    /**
     * Get the current academic year.
     */
    private function getCurrentAcademicYear()
    {
        return date('Y');
    }
}