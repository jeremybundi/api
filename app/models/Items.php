<?php

use Phalcon\Mvc\Model;

class Items extends Model
{
    public $id;
    public $item_name;
    public $item_url;
    public $details;
    public $price;
    public $category_id;
    public $subcategory_id;

    public function category()
    {
        return $this->belongsTo(
            "Categories",
            "category_id",
            "id"
        );
    }

    public function subcategory( bool $with = false)
    {
        return $this->hasOne(
            "Subcategories",
            "id",
            "subcategory_id",
            $with
        );
    }

    public static function getAllItems()
    {
        return self::find();
    }

}
