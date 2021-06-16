<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\PasswordReset;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name',
        'explanation',
        'password',
        'email',
        'island_id',
        'rank',
        'is_type',
        'is_comment',
        'is_comment_type',
        'type_role',
        'comment_type_role',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token){
        $this->notify(new PasswordReset($token));
    }

    public function sellerContact(){
        return $this->hasOne(SellerContact::class,'user_id','id');
    }

    public function userIslands(){
        return $this->hasMany('App\UserIsland');
    }

    public function userPrefecture(){
        return $this->hasMany('App\UserPrefectre');
    }

    public function csvSettings(){
        return $this->belongsToMany('App\CsvSetting');
    }

    public function localvendorSellers(){
        return $this->hasMany('App\LocalvendorSeller','user_id','id');
    }

    public function localvendorEcmallId(){
        return $this->hasMany('App\LocalvendorEcmallId', 'localvendor_id','id');
    }
    
}
