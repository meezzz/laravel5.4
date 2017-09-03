<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //关联：即别的模型和当前模型关联，当前模型对于其他模型的数据，都是唯一的，即别的模型属于当前模型某条数据
    //一对一关联
    public function userinfo(){
        //参数1：关联模型命名空间，参数2：关联外键
        return $this->hasOne('App\Model\Userinfo','user_id');
    }
    //一对多关联
    public function post(){
        //参数1：关联模型命名空间，参数2：关联外键
        return $this->hasMany('App\Model\Post','user_id');
    }

    //属于关系创建，当前模型属于其他模型的某条数据
    public function country(){
        //参数1：关联模型命名空间，参数2：关联外键
        return $this->belongsTo('App\Model\Country','country_id');
    }

    //多对多关系
    public function group(){
        /**
         * 参数1：于本模型多对多对应的模型位置
         * 参数2：多对多会有个中间表，参数为中间表名称
         * 参数3：当前模型与中建表关联的外键
         * 参数4：关联的多对多表模型与中间表的关联外键
         */
        return $this->belongsToMany('App\Model\Group','group_user','user_id','group_id');
    }
}
