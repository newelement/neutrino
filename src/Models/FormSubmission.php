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
        if( $this->private ){
            return Crypt::decryptString( json_decode($value)->data );
        } else {
            return Crypt::decryptString( json_decode($value) );
        }
    }

    public function getFilesAttribute($value)
    {
        if( $this->private ){
            return Crypt::decryptString( json_decode($value)->data );
        } else {
            return Crypt::decryptString( json_decode($value) );
        }
    }

}
