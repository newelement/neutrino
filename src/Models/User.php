<?php
namespace Newelement\Neutrino\Models;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Newelement\Neutrino\Contracts\User as UserContract;
use Newelement\Neutrino\Traits\NeutrinoUser;

class User extends Authenticatable implements UserContract
{
    use NeutrinoUser;
    protected $guarded = [];
    public $additional_attributes = ['locale'];

    public function getAvatarAttribute($value)
    {
        return $value ?? config('shoppe.user.default_avatar', '/vendor/newelement/shoppe/images/default.png');
    }
    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }
    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = $value->toJson();
    }
    public function getSettingsAttribute($value)
    {
        return collect(json_decode($value));
    }
    public function setLocaleAttribute($value)
    {
        $this->settings = $this->settings->merge(['locale' => $value]);
    }
    public function getLocaleAttribute()
    {
        return $this->settings->get('locale');
    }
}
