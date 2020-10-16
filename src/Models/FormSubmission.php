<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class FormSubmission extends Model
{
    use SoftDeletes;

    public function getFieldsAttribute($value)
    {
        return Crypt::decryptString( json_decode($value)->data );
    }

    public function getFilesAttribute($value)
    {
        return Crypt::decryptString( json_decode($value)->data );
    }

}
