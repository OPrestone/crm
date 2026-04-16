<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCampaignContact extends Model
{
    protected $fillable = ['campaign_id','contact_id','sent_at','opened_at','clicked_at'];

    protected $casts = [
        'sent_at'    => 'datetime',
        'opened_at'  => 'datetime',
        'clicked_at' => 'datetime',
    ];

    public function campaign() { return $this->belongsTo(EmailCampaign::class); }
    public function contact()  { return $this->belongsTo(Contact::class); }
}
