<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;

class ActivityController extends Controller
{
    public function index(){
        $activities = Activity::all();
        return inertia('Admin/activity', [
            'activities' => $activities
        ]);
    }


    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:activity,event',
            'type' => 'required|in:academic,sports,cultural,meeting,ceremony,club,other',
            'start_date' => 'required|date|after:now',
            'end_date' => 'nullable|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'target_classes' => 'required|array',
            // 'max_participants' => 'nullable|integer|min:1',
            'requires_registration' => 'boolean',
            'registration_deadline' => 'nullable|date|before:start_date',
            'is_mandatory' => 'boolean',
            'requirements' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'scheduled';



        $activity = Activity::create($validated);

        return redirect()->route('admin.activity.index')
                        ->with('success', ucfirst($activity->category) . ' created successfully!');
    }


    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:activity,event',
            'type' => 'required|in:academic,sports,cultural,meeting,ceremony,club,other',
            'start_date' => 'required|date|after:now',
            'end_date' => 'nullable|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'target_classes' => 'required|array',
            // 'max_participants' => 'nullable|integer|min:1',
            'requires_registration' => 'boolean',
            'registration_deadline' => 'nullable|date|before:start_date',
            'is_mandatory' => 'boolean',
            'requirements' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $activity = Activity::findOrFail($id);

        $activity->update([
           'title' => $request->title,
           'description' => $request->description,
           'category' => $request->category,
           'type' => $request->type,
           'start_date' => $request->start_date,
           'end_date' => $request->end_date,
           'location' => $request->location,
           'target_classes' => $request->target_classes,
           'requires_registration' => $request->requires_registration,
           'registration_deadline' => $request->registration_deadline,
           'is_mandatory' => $request->is_mandatory,
           'requirements' => $request->requirements,
           'image' => $request->image,
        ]);

        $activity->save();

        return redirect(route('admin.activity.index'));

    }

     public function destroy(Activity $activity)
    {
        $activity->delete();
    
        return redirect()->back()->with('message', [
            'type'        => 'success',
            'description' => '',
            'title'       => 'Record Deleted',
        ]);
    }
}
