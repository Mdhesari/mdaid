<?php

namespace App\Application\Telegram\Commands;

use Carbon\Carbon;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

// use App\Model\User;

class Start extends Command
{

    protected $name = "start";

    protected $description = "شروع کار با ربات";

    protected $aliases = ['شروع'];

    /**
     * handle
     *
     * @param  mixed $arguments
     *
     * @return void
     */
    public function handle()
    {

        $message = $this->update->getMessage();

        user()->save($message);

        $keyboard = keyboard('home');

        $this->replyWithMessage(['text' => renderText('start'), 'reply_markup' => $keyboard]);
    }
}
