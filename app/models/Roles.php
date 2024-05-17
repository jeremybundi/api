<?php
use Phalcon\Mvc\Model;

class Roles extends Model
{
    public $id;
    public $name;

    public function initialize()
    {
        $this->setSource('roles');
        // Define relationships (if needed)
        // ...
    }
}
