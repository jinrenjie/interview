<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Date: 19/10/2024
 *
 * @author George
 * @package App\Models
 */
class Journal extends Model
{
    use HasFactory;
    
    /**
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * @var string[]
     */
    protected $fillable = ['sql', 'error', 'user_id'];
}
