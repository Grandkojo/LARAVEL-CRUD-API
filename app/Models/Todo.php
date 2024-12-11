<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    const STATUS_NOT_STARTED = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_COMPLETED = 2; 
    

    /**
     * sets the readable correspondent of a status
     * 
     * @return array<string> the mapping of status codes to status labels
     */
    public static function statusLabels(){
        return [
            self::STATUS_NOT_STARTED => 'NOT STARTED',
            self::STATUS_IN_PROGRESS => 'IN PROGRESS',
            self::STATUS_COMPLETED => 'COMPLETED',
        ];
    }

    /**
     * this function gets the human readable form of the status
     * @return string
     */
    public function getStatusLabelAttribute() : string {
        $labels = self::statusLabels();

        return $labels[$this->status] ?? null;
    }

    /**
     * attrbutes that are mass assignable
     * @var list<string>
     */

    protected $fillable = [
        'title',
        'details',
        'details'
    ];
}
