<?php

namespace App\Application\Contracts;

interface FileHandlerInterface
{

    public function write($content);

    public function read();

    public function edit($content);

    public function remove();

    public function close();
}
