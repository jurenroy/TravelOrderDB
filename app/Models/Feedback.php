<?php

namespace App\Models;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    
    protected $table = 'feedbacks';
    
    protected $primaryKey = 'feedbackid'; // Specify the primary key if it's not 'id'

    protected $fillable = [
        'referenceid',
        'evaluation1',
        'evaluation2',
        'evaluation3',
        'evaluation4',
        'date',
    ];

    // Define any relationships if necessary
    public function service()
    {
        return $this->belongsTo(Service::class, 'referenceid', 'id');
    }
}
