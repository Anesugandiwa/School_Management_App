<?php

namespace App\Http\Controllers;
use App\Models\Klass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimeTable;
use Illuminate\Http\Request;

class TimeTableController extends Controller
{
    public function index(){
        // $klasses =Klass::all();
        return inertia('Admin/timeTable',[
            'klasses' => Klass::all(),
            'subjects' =>Subject::all(),
            'teachers' => Teacher::all(),
                
            
        ]);
    }
        // ADD THIS METHOD - This was missing
    private function getDefaultTimeSlots()
    {
        return [
            ['id' => 1, 'name' => 'Period 1', 'start_time' => '08:00', 'end_time' => '08:40'],
            ['id' => 2, 'name' => 'Period 2', 'start_time' => '08:40', 'end_time' => '09:20'],
            ['id' => 3, 'name' => 'Break', 'start_time' => '09:20', 'end_time' => '09:40', 'isBreak' => true],
            ['id' => 4, 'name' => 'Period 3', 'start_time' => '09:40', 'end_time' => '10:20'],
            ['id' => 5, 'name' => 'Period 4', 'start_time' => '10:20', 'end_time' => '11:00'],
            ['id' => 6, 'name' => 'Period 5', 'start_time' => '11:00', 'end_time' => '11:40'],
            ['id' => 7, 'name' => 'Lunch', 'start_time' => '11:40', 'end_time' => '12:20', 'isBreak' => true],
            ['id' => 8, 'name' => 'Period 6', 'start_time' => '12:20', 'end_time' => '13:00'],
            ['id' => 9, 'name' => 'Period 7', 'start_time' => '13:00', 'end_time' => '13:40']
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'klass_id' => 'required|exists:klasses,id',
        'academic_year' => 'required|string',
        'day_of_week' =>'nullable',
       'timetable.*.*.subject_id'  => 'required|exists:subjects,id',
        'term' => 'required|in:1st Term,2nd Term,3rd Term',
        'timetable' => 'required|array',
        'timetable.*' => 'array',
        'timetable.*.*' => 'array',
        'timetable.*.*.subject_id' => 'nullable|exists:subjects,id',
        'timetable.*.*.teacher_id' => 'nullable|exists:users,id',
         // slots 
        'time_slots' => 'required|array',
        'time_slots.*.id' => 'required|integer',
        'time_slots.*.name' => 'required|string',
        'time_slots.*.start_time' => 'required|string',
        'time_slots.*.end_time' => 'required|string',
        'time_slots.*.isBreak' => 'sometimes|boolean'
    ]);
    $timeSlots = $request->time_slots;

    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']; // You can adjust this
   

    foreach ($daysOfWeek as $day) {
        if (!isset($request->timetable[$day])) continue;

        foreach ($request->timetable[$day] as $periodId => $periodData) {
            $timeSlot = collect($timeSlots)->firstWhere('id', $periodId);

            if (!$timeSlot || ($timeSlot['isBreak'] ?? false)) continue;

            // Only create timetable entry if subject is assigned
            if (!empty($periodData['subject_id'])) {
                Timetable::create([
                    'klass_id' => $request->klass_id,
                    'day_of_week' => $day,
                    'period' => $periodId,
                    'period_name' => $timeSlot['name'],
                    'start_time' => $timeSlot['start_time'],
                    'end_time' => $timeSlot['end_time'],
                    'subject_id' => $periodData['subject_id'],
                    'teacher_id' => $periodData['teacher_id'] ?? null,
                    'academic_year' => $request->academic_year,
                    'term' => $request->term,
                    'is_break' => false
                ]);
            }
        }
    }

    return redirect(route('admin.timeTable.index'))->with('success', 'Timetable saved successfully.');
}
public function formatTimetableForFrontend($timetable)
{
    $formatted = [];
    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    $timeSlots = $this->getDefaultTimeSlots();

    // Initialize empty structure
    foreach ($daysOfWeek as $day) {
        $formatted[$day] = [];
        foreach ($timeSlots as $slot) {
            // Only include non-break slots
            if (!($slot['isBreak'] ?? false)) {
                $formatted[$day][$slot['id']] = [
                    'subject_id' => null,
                    'teacher_id' => null,
                    'subject_name' => '',
                    'teacher_name' => ''
                ];
            }
        }
    }

    // Fill with actual data
    foreach ($timetable as $entry) {
        // Fix: Use day_of_week instead of day
        $day = ucfirst(strtolower($entry->day_of_week)); // ✅ Changed from $entry->day
        $period = $entry->period;

        // Check if the day and period exist in the structure
        if (isset($formatted[$day][$period])) {
            $formatted[$day][$period] = [
                'subject_id' => $entry->subject_id,
                'teacher_id' => $entry->teacher_id,
                'subject_name' => $entry->subject?->name ?? '',
                'teacher_name' => $entry->teacher?->first_name ?? ''
            ];
        }
    }

    return $formatted;
}

public function fetch(Request $request)
{
    $request->validate([
        'klass_id' => 'required|exists:klasses,id',
        'term' => 'required|in:1st Term,2nd Term,3rd Term',
        'academic_year' => 'required|string'
    ]);

    $timetable = Timetable::where('klass_id', $request->klass_id)
        ->where('term', $request->term)
        ->where('academic_year', $request->academic_year)
        ->with(['subject:id,name', 'teacher:id,first_name'])
        ->orderBy('day_of_week')
        ->orderBy('period')
        ->get();

    if ($timetable->isEmpty()) {
        return response()->json([
            'timetable' => null,
            'message' => 'No timetable found'
        ], 404);
    }

    // Format the timetable for frontend
    $formattedTimetable = $this->formatTimetableForFrontend($timetable);

    return response()->json([
        'timetable' => $formattedTimetable,
        'message' => 'Timetable loaded successfully'
    ]);
}

}
