<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = ['tenant_id', 'template_id', 'contact_id', 'name', 'data', 'created_by'];
    protected $casts = ['data' => 'array'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function template() { return $this->belongsTo(CardTemplate::class, 'template_id'); }
    public function contact() { return $this->belongsTo(Contact::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
