<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Klass;
use App\Models\Student;
use App\Models\TimeTable;

class StudentTableController extends Controller
{
    /**
     * Display the student's timetable page.
     */
    public function studentIndex()
    {
        $student = auth()->user();

        $klass = Klass::find($student->klass_id);

        return inertia('Students/table', [
            'klass' => $klass,
            'currentTerm' => $this->getCurrentTerm(),
            'academicYear' => $this->getCurrentAcademicYear(),
            'error' => null
        ]);
    }

    /**
     * Fetch the student's timetable.
     */
    public function studentFetch(Request $request)
    {
        $student = $this->getAuthenticatedStudent();

        if (!$student || !$student->klass_id) {
            return response()->json([
                'error' => 'No class assigned to your account.',
                'timetable' => null
            ], 400);
        }

        $validated = $request->validate([
            'term' => 'required|in:1st Term,2nd Term,3rd Term',
            'academic_year' => 'required|string'
        ]);

        $timetableEntries = $this->getTimetableEntries(
            $student->klass_id,
            $validated['term'],
            $validated['academic_year']
        );

        if ($timetableEntries->isEmpty()) {
            return response()->json([
                'timetable' => null,
                'message' => 'No timetable found for your class'
            ], 404);
        }

        $formattedTimetable = $this->formatStudentTimetable($timetableEntries);

        return response()->json([
            'timetable' => $formattedTimetable,
            'message' => 'Timetable loaded successfully',
            'klass' => $student->klass
        ]);
    }

    /**
     * Get the authenticated student.
     */
    private function getAuthenticatedStudent()
    {
        $user = auth()->user();

        return Student::where('user_id', $user->id)->first();
    }

    /**
     * Get the timetable entries for the given class, term, and academic year.
     */
    private function getTimetableEntries($klassId, $term, $academicYear)
    {
        return TimeTable::where('klass_id', $klassId)
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->with(['subject:id,name', 'teacher:id,first_name,last_name'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Format timetable entries into a structured timetable.
     */
    private function formatStudentTimetable($timetableEntries)
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
                $teacherName = $entry->teacher
                    ? trim($entry->teacher->first_name . ' ' . ($entry->teacher->last_name ?? ''))
                    : '';

                $formatted[$day][] = [
                    'period_name' => $entry->period_name,
                    'start_time' => $entry->start_time,
                    'end_time' => $entry->end_time,
                    'subject_name' => $entry->subject?->name ?? '',
                    'teacher_name' => $teacherName,
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
