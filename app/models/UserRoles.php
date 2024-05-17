<?php
use Phalcon\Mvc\Model;

class UserRoles extends Model
{
    public $user_id;
    public $role_id;

    public function initialize()
    {
        $this->setSource('user_roles');
        
        $this->belongsTo('user_id', 'Users', 'id');
        $this->belongsTo('role_id', 'Roles', 'id');
    }
}
