<?php

namespace App\Helper;

class TextEditor
{

    protected $line = PHP_EOL;

    protected $content = [];

    public function text($text)
    {

        $this->content[] = $text;

        return $this;
    }

    public function join($text)
    {

        $this->content[count($this->content) - 1] .= $text;

        return $this;
    }

    public function line()
    {

        $this->join($this->line);

        return $this;
    }

    public function space()
    {

        $this->join(" ‎‎‎&#32 ");

        return $this;
    }

    public function lineAndSpace()
    {

        $this->join($this->line . "‎‎‎&#32");

        return $this;
    }

    public function output()
    {
        $output = join(PHP_EOL, $this->content);

        $this->clear();
        return $output;
    }

    public function clear()
    {

        $this->content = [];

        return $this;
    }
}
