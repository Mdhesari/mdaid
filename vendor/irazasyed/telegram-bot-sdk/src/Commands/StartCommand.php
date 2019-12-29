<?php

namespace Telegram\Bot\Commands;


use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{

    protected $name = "start";

    protected $description = "start command";

    public function handle()
    {

        $this->replyWithMessage(['text' => $this->description]);

        
    }
}
