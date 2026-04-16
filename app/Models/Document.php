<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'title', 'file_name', 'file_path', 'file_type', 'file_size',
        'category', 'description', 'documentable_id', 'documentable_type', 'uploaded_by',
    ];

    protected $casts = ['file_size' => 'integer'];

    public function tenant()       { return $this->belongsTo(Tenant::class); }
    public function uploader()     { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function documentable() { return $this->morphTo(); }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function getFileIconAttribute(): string
    {
        return match(true) {
            str_contains($this->file_type ?? '', 'pdf')   => 'bi-file-earmark-pdf-fill text-danger',
            str_contains($this->file_type ?? '', 'word')  => 'bi-file-earmark-word-fill text-primary',
            str_contains($this->file_type ?? '', 'excel') => 'bi-file-earmark-excel-fill text-success',
            str_contains($this->file_type ?? '', 'image') => 'bi-file-earmark-image-fill text-info',
            str_contains($this->file_type ?? '', 'zip')   => 'bi-file-earmark-zip-fill text-warning',
            default => 'bi-file-earmark-fill text-secondary',
        };
    }
}
