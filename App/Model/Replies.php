<?php

namespace App\Model;

use App\Helper\DB;

class Replies extends DB
{

    protected $table = "replies";

    public static function render($name)
    {

        $instance = new Replies;

        $result = $instance->find('name', $name);

        if (!$result)
            return "null";

        return $result->text;
    }
}
