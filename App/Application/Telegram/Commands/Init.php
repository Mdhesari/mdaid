<?php

namespace App\Application\Telegram\Commands;

use App\Application\State\Work;
use App\Model\Budget;
use Carbon\Carbon;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Helpers\Emojify;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Types;

// use App\Model\User;

class Init extends Command
{

    protected $name = "init";

    protected $description = "شروع کار";

    protected $aliases = ['آمار امروز', 'بازگشت به پنل','panel'];

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

        $chat = $message->getChat();

        // change current state of the user and user will be passed to Work::class
        user()->setState(Work::getName(), $chat->getId());

        // we know the user is already saved
        $user = user()->find('user_id', $chat->getId());

        $budget = new Budget;

        $today = Carbon::today();

        // get today records from user
        $budgets = $budget
            ->where('user_id', $user->id)
            ->where('created_at', $today, '>=')
            ->all();

        $total = 0;
        $income = 0;
        $expense = 0;

        foreach ($budgets as $child) {

            if ($child->isIncome) {

                $total += (int) $child->amount;
                $income += (int) $child->amount;
            } else {

                $total -= (int) $child->amount;
                $expense += (int) $child->amount;
            }
        }

        $fullname = $user->full_name;

        $text = textEditor()

            ->text(verta($today)->format('%d %B، %Y'))
            ->text(" سلام، خوش آمدید ")
            ->join($fullname)
            ->line()
            ->text("آمار درآمد و خرج امروز : ")
            ->text($total)
            ->join(" toman ")
            ->text("درآمد : ")
            ->line()
            ->join($income)
            ->join(" toman ")
            ->text("خرج : ")
            ->line()
            ->join($expense)
            ->join(" toman ")
            ->output();


        $reply_markup = keyboard(

            Keyboard::inlineButton(['text' => 'بازگشت']),
            Keyboard::inlineButton(['text' => 'راهنما'])
        )->row(

            Keyboard::inlineButton(['text' => 'آمار امروز']),
            Keyboard::inlineButton(['text' => 'مدیریت مالی ' . Emojify::text('hammer_and_wrench')])
        )->setResizeKeyboard(true)->setOneTimeKeyboard(true);



        if ($this->update->isType(Types::CALLBACK_QUERY)) {

            $message = $this->update->getCallbackQuery()->getMessage();

            $this->telegram->editMessageText([
                'chat_id' => $message->getChat()->getId(),
                'text' => $text,
                'message_id' => $message->getMessageId(),
            ]);

            $next_text = renderText('initalize');

            $this->replyWithMessage(['text' => $next_text, 'reply_markup' => $reply_markup]);
        } else {

            $this->replyWithMessage(compact('text', 'reply_markup'));
        }
    }
}
