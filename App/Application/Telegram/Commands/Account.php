<?php

namespace App\Application\Telegram\Commands;

use App\Application\State\Finance;
use App\Model\Budget;
use Carbon\Carbon;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Helpers\Emojify;
use Telegram\Bot\Keyboard\Keyboard;

class Account extends Command
{

    protected $name = 'account';

    protected $description = 'مدیریت کل حساب';

    protected $aliases = ['بررسی کل', 'مدیریت مالی'];

    public function __construct()
    {

        $this->aliases[] = 'مدیریت مالی ' . Emojify::text('hammer_and_wrench');
    }

    public function handle()
    {

        $message = $this->update->getMessage();

        $chat = $message->getChat();

        // change current state of the user and user will be passed to Work::class
        user()->setState(Finance::getName(), $chat->getId());

        $reply_markup = keyboard()
            ->row(
                Keyboard::inlineButton(['text' => 'بازگشت به پنل'])
            )
            ->row(
                Keyboard::inlineButton(['text' => 'هزینه ها ' . Emojify::text('heavy_minus_sign')]),
                Keyboard::inlineButton(['text' => 'درآمد ها ' . Emojify::text('heavy_plus_sign')])
            )
            ->row(
                Keyboard::inlineButton(['text' => 'پرداخت نشده ها']),
                Keyboard::inlineButton(['text' => 'پرداخت شده ها'])
            )
            ->row(
                Keyboard::inlineButton(['text' =>  'کل آمار ' . Emojify::text('bar_chart')])
            )
            ->inline()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true);


        $budget = new Budget;

        $today = Carbon::today();

        // we know the user is already saved
        $user = user()->find('user_id', $chat->getId());

        $records = $budget
            ->where('user_id', $user->id)
            ->where('created_at', $today, '>=')
            ->all();

        $my_text = textEditor("اطلاعات ثبت شده امروز ")->join(verta($today)->format('%d %B، %Y'))->line();

        $text = customTextEditor()->renderMultiRecord($records, $my_text);

        $text = $text->output();

        $parse_mode = 'HTML';

        $this->replyWithMessage(compact('text', 'reply_markup', 'parse_mode'));
    }
}
