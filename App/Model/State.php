<?php

namespace App\Model;

use App\Helper\DB;

class State extends DB
{

    protected $table = 'states';

    const HOME = 'home';
    const WORK = 'work';

    public function getState($name)
    {

        return $this->find('name', $name);
    }
}
