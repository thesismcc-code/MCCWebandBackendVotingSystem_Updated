<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fingerprint extends Model
{
    protected $table = 'fingerprints';
    protected $fillable = ['fid', 'name', 'template'];
}
