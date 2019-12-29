<?php

namespace App\Application\Contracts;

use App\Model\Budget;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Helpers\Emojify;
use Telegram\Bot\Keyboard\Keyboard;

abstract class Filter extends Command
{

    const PAGINATION = 5;

    protected $isEditMessage = false;

    protected $andOperator = true;

    protected $filterByUser = true;

    protected $name = 'filter';

    protected $user = null;

    protected $records = null;

    protected $limits = self::PAGINATION . ", 0";

    protected $replyCallbackDataType = 'filter';

    protected $total = 0;

    public function setLimits($limits)
    {

        $this->limits = $limits;
    }

    public function getType()
    {
        $class_name = \explode('\\', \get_called_class());

        return $class_name[count($class_name) - 1];
    }

    public function isEditedMessage()
    {

        $this->isEditMessage = true;
    }

    public function handle()
    {

        $budget = new Budget;

        $message = $this->update->getMessage();

        $chat = $message->getChat();

        // we know the user is already saved
        $this->user = user()->find('user_id', $chat->getId());

        $filters = $this->filter();

        if ($this->filterByUser) {

            $budget->where('user_id', $this->user->id);
        }

        foreach ($filters as $key => $value) {

            if (is_bool($value)) {

                $value = (int) $value;
            }

            $math_operator = '=';

            if (str_contains($key, 'created_at')) {

                $math_operator = '>=';

                if ($key == 'created_at_2' || $key == 'created_at_smaller') {
                    $math_operator = '<=';
                    $key = 'created_at';
                }
            }

            if ($this->andOperator)
                $budget->where($key, $value, $math_operator);
            else
                $budget->orWhere($key, $value, $math_operator);
        }

        $budgetCounter = clone $budget;

        $total = $this->total = $budgetCounter->count();

        $limits = explode(",", $this->limits);

        if (isset($limits[1])) {

            $limits[0] = $limits[0] . " OFFSET " . $limits[1];
        }

        $budget->orderBy('created_at');

        $budget->limit($limits[0]);

        $this->records = $budget->all();

        $text = $this->renderText();

        $parse_mode = 'HTML';

        $keyboards = [];

        $reply_markup = $this->renderReplymarkup($total);

        $message_output = [];

        if (is_null($reply_markup))
            $message_output = compact('text', 'parse_mode');
        else
            $message_output = compact('text', 'reply_markup', 'parse_mode');

        if ($this->isEditMessage) {

            $message = $this->update->getMessage();

            $chat_id = $message->getChat()->getId();

            $message_id = $message->getMessageId();

            $message_output = array_merge($message_output, compact('chat_id', 'message_id'));

            $this->telegram->editMessageText($message_output);
        } else {

            $this->replyWithMessage($message_output);
        }
    }

    protected function renderReplymarkup($total_records)
    {

        $page = explode(",", $this->limits);

        $last_limit = (int) $page[count($page) - 1];

        $keyboards = [];

        $filterType = $this->getType();

        fileHandler()->varDump($last_limit);

        if ($last_limit >= self::PAGINATION) {

            $keyboards[] = Keyboard::inlineButton([
                'text' => textEditor(Emojify::text('arrow_left'))->join(" صفحه قبل ")->output(),
                'callback_data' => textEditor($this->replyCallbackDataType)->join("prevPage")->join("?page={$last_limit}")->join("&type=" . $filterType)->output()
            ]);
        }

        if ($last_limit > self::PAGINATION || $last_limit < $total_records) {

            $page_num = $last_limit / self::PAGINATION;

            if ($page_num > 1)
                $keyboards[] = Keyboard::inlineButton([
                    'text' => "صفحه " . Emojify::text($page_num),
                    'callback_data' => 'null'
                ]);
        }

        if (($last_limit + self::PAGINATION) < $total_records) {

            $keyboards[] = Keyboard::inlineButton([
                'text' => textEditor(Emojify::text('arrow_right'))->join(" صفحه بعد ")->output(),
                'callback_data' => textEditor($this->replyCallbackDataType)->join("nextPage")->join("?page={$last_limit}")->join("&type=" . $filterType)->output()
            ]);
        }

        $hasKeyboards = count($keyboards) > 0;

        return $hasKeyboards ? keyboard()->inline()->row($keyboards) : null;
    }

    protected function renderText(): string
    {

        $text = customTextEditor()->renderMultiRecord($this->records)->output();

        return $text;
    }

    abstract public function filter();
}
