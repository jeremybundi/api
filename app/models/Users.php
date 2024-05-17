<?php
use Phalcon\Mvc\Model;

class Users extends Model
{
    public $id;
    public $name;
    public $email;
    public $phone;
    public $username;
    public $password;

    public function initialize()
    {
        $this->setSource('users');

        $this->hasMany('id', 'UserRoles', 'user_id');
    }
    public static function addItem($userData)
    {
        $user = new self();
        $user->name = $userData['name'];
        $user->phone = $userData['phone'];
        $user->email = $userData['email'];
        $user->username = $userData['username'];
        $user->password = $userData['password'];
  
        if ($user->save()) {
            return true;
        } else {
            return false;
        }
    }
}
