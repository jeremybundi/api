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
        // Define relationships (if needed)
        // ...
    }
}