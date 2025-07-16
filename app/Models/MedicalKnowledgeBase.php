<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalKnowledgeBase extends Model
{
    use HasFactory;

    protected $table = 'medical_knowledge_base';

    protected $fillable = [
        'category',
        'title',
        'description',
        'keywords',
        'detailed_info',
        'severity_level',
        'requires_doctor',
        'recommendations',
        'related_topics',
        'is_active'
    ];

    protected $casts = [
        'keywords' => 'array',
        'related_topics' => 'array',
        'requires_doctor' => 'boolean',
        'is_active' => 'boolean',
    ];
}