<?php


namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


class Subscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
        'start_date',
        'end_date'
    ];

    protected $table = 'subscription';
}
