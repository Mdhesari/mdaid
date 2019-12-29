<?php

namespace App\Application\Telegram\Commands;

use App\Model\State;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Types;

/**
 * Class HelpCommand.
 */
class Help extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'help';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['راهنما'];

    /**
     * @var string Command Description
     */
    protected $description = 'راهنما';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {

        $message = $this->update->getMessage();

        $text = renderText('help');

        if ($this->update->isType(Types::CALLBACK_QUERY)) {

            $message = $this->update->getCallbackQuery()->getMessage();

            $reply_markup = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'بازگشت', 'callback_data' => 'back'])
                );

            $this->telegram->editMessageText([
                'chat_id' => $message->getChat()->getId(),
                'text' => $text,
                'message_id' => $message->getMessageId(),
                'reply_markup' => $reply_markup
            ]);
        } else {

            $this->replyWithMessage(compact('text'));
        }
    }
}
