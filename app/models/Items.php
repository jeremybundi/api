<?php 
use Phalcon\Mvc\Model;

class Items extends Model
{
    public $id;
    public $item_name;
    public $item_url;
    public $details;
    public $price;

    // Retrieve all items
    public static function getAllItems()
    {
        return self::find();
    }

}
