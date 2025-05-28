<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        
        'title',
        'description',
        'category',
        'type',
        'start_date',
        'end_date',
        'location',
        'target_classes',
        'max_participants',
        'requires_registration',
        'registration_deadline',
        'is_mandatory',
        'requirements',
        'status',
    ];
    
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'target_classes' => 'array',
        'requires_registration' => 'boolean',
        'is_mandatory' => 'boolean'
    ];
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // public function registrations(): HasMany
    // {
    //     return $this->hasMany(ActivityRegistration::class);
    // }

    // public function registeredStudents()
    // {
    //     return $this->belongsToMany(Student::class, 'activity_registrations')
    //                 ->withPivot('registered_at', 'status', 'notes')
    //                 ->withTimestamps();
    // }

    // Scopes
    // public function scopeActive($query)
    // {
    //     return $query->where('status', '!=', 'cancelled');
    // }

    // public function scopeUpcoming($query)
    // {
    //     return $query->where('start_date', '>', now());
    // }

    // public function scopeByCategory($query, $category)
    // {
    //     return $query->where('category', $category);
    // }

    // public function scopeByType($query, $type)
    // {
    //     return $query->where('type', $type);
    // }

    // Accessors
    // public function getIsExpiredAttribute()
    // {
    //     return $this->end_date ? now() > $this->end_date : now() > $this->start_date;
    // }

    // public function getCanRegisterAttribute()
    // {
    //     if (!$this->requires_registration) return false;
    //     if ($this->registration_deadline && now() > $this->registration_deadline) return false;
    //     if ($this->max_participants && $this->registrations()->count() >= $this->max_participants) return false;
    //     if ($this->is_expired) return false;
        
    //     return true;
    // }
}
