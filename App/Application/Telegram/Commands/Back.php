<?php

namespace App\Application\Telegram\Commands;

use App\Application\State\Home;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Types;

// use App\Model\User;

class Back extends Command
{

    protected $name = "back";

    protected $description = "بازگشت به خانه";

    protected $aliases = ['بازگشت'];

    /**
     * handle
     *
     * @param  mixed $arguments
     *
     * @return void
     */
    public function handle()
    {

        $chat = $this->update->getMessage()->getChat();

        if (user()->setState(Home::getName(), $chat->getId())) { }

        if ($this->update->isType(Types::CALLBACK_QUERY)) {

            $message = $this->update->getCallbackQuery()->getMessage();

            $text = renderText('start');

            $reply_markup = keyboard('home');

            $this->telegram->editMessageText([
                'chat_id' => $message->getChat()->getId(),
                'text' => $text,
                'message_id' => $message->getMessageId(),
                'reply_markup' => $reply_markup
            ]);
        } else {

            $keyboard = keyboard('home');

            $keyboard_remove = Keyboard::remove();

            $new_message = $this->replyWithMessage(['text' => 'درحال بازگشت...', 'reply_markup' => $keyboard_remove]);

            $this->telegram->deleteLastMessage($new_message);

            $this->replyWithMessage(['text' => renderText('start'), 'reply_markup' => $keyboard]);
        }
    }
}
