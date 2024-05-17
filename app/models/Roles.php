<?php
use Phalcon\Mvc\Model;

class Roles extends Model
{
    public $id;
    public $name;

    public function initialize()
    {
        $this->setSource('roles');


        $this->hasMany('id', 'UserRoles', 'role_id');
    }
}
