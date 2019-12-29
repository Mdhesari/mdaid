<?php

namespace App\Application\State;

use App\Application\Telegram\Commands\Back;
use App\Application\Telegram\Commands\Help;
use App\Application\Telegram\Commands\Init;
use App\Application\Telegram\Commands\Start;
use Telegram\Bot\Types;

class Home extends BaseState
{

    /**
     * run individual task
     *
     * @return void
     */
    public function execute()
    {

        $this->app->bot->addCommands([
            Start::class,
            Help::class,
            Back::class,
            Init::class,
        ]);

        parent::execute();
    }
}
