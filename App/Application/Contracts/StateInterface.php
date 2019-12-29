<?php

namespace App\Application\Contracts;

interface StateInterface
{

    /**
     * run individual task
     *
     * @return void
     */
    public function execute();
}
