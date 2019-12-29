<?php

namespace App\Application\State;

use App\Application\Telegram\Commands\Account;
use App\Application\Telegram\Commands\Back;
use App\Application\Telegram\Commands\Help;
use App\Application\Telegram\Commands\Init;
use App\Helper\DB;
use App\Model\Budget;
use Carbon\Carbon;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Types;

class Work extends BaseState
{

    public function execute()
    {

        $this->telegram->addCommands([
            Back::class,
            Help::class,
            Init::class,
            Account::class
        ]);

        if (parent::execute() !== false) {

            return;
        }

        $message = $this->update->getMessage();

        if ($this->update->isType(Types::CALLBACK_QUERY)) {

            $callback_query = $this->update->getCallbackQuery();

            $data = $callback_query->getData();

            $callback_id = $callback_query->getId();

            $request = renderQueries($data);

            if (str_contains($data, 'budgetRemove')) {

                $id = $request['id'];

                $budget = new Budget;

                $result = $budget->where('id', $id)->delete();

                if ($result) {

                    $this->telegram->answerCallbackQuery(['callback_query_id' => $callback_id, 'text' => renderText('budgetSuccessfulRemove')]);

                    $this->telegram->deleteMessage(['chat_id' => $message->getChat()->getId(), 'message_id' => $message->getMessageId()]);
                } else {

                    $this->notifyError("\n Error on deleting from db budget occured.");
                }
            } else if (str_contains($data, 'budgetUpdatePaid')) {

                $id = $request['id'];

                $budget = new Budget;

                $user_budget = $budget->find('id', $id);

                $isPaid = !(boolval($user_budget->isPaid));

                $result = $budget->where('id', $id)->update([
                    'isPaid' => (int) $isPaid
                ]);

                if ($result !== false) {

                    $user_budget->isPaid = $isPaid;

                    $text = customTextEditor()->renderNewRecord($user_budget);

                    $this->telegram->answerCallbackQuery(['callback_query_id' => $callback_id, 'text' => renderText('budgetSuccessfulUpdatePaid')]);

                    $reply_markup = keyboard()->inline()->row(
                        Keyboard::inlineButton(['text' => 'پاکش کن', 'callback_data' => "budgetRemove?id={$user_budget->id}"]),
                        Keyboard::inlineButton(['text' => $isPaid ? "پرداخت نشده" : "پرداخت شده", 'callback_data' => "budgetUpdatePaid?old={$isPaid}&id={$user_budget->id}"])
                    );

                    $this->telegram->editMessageText(['text' => $text, 'chat_id' => $message->getChat()->getId(), 'message_id' => $message->getMessageId(), 'reply_markup' => $reply_markup]);
                } else {

                    $this->notifyError("\n Error on updating db budget occured.");
                }
            }

            return 1;
        }


        $text = trim($message->getText());

        $status = true;

        if (!starts_with($text, ['+', '-'])) {

            $status = false;
        }

        // remove +|- sign 
        $sign = $text[0];

        $text = substr($text, 1, strlen($text));

        $text = explode('-', $text);

        $amount = (int) trim($text[0]);

        if ($status)
            $status = $amount !== 0;

        $description = null;

        $name = null;

        if ($status == false) {
            $text = renderText('invalidValue');

            return $this->replyWithMessage(compact('text'));
        }

        if (isset($text[1])) {

            $desc = $text[1];

            $pos = strpos($desc, ':');

            if ($pos !== false) {

                $description = trim(substr($desc, 0, $pos));

                $name = trim(substr($desc, $pos + 1, strlen($desc)));
            } else {

                $description = trim($text[1]);
            }
        }

        $budget = new Budget;

        $user_id = $message->getChat()->getId();

        $user = user()->find('user_id', $user_id);

        $isIncome = true;

        if ($sign == '-') {
            // income money
            $isIncome = false;
        }

        $created_at = Carbon::now();

        $result = $budget->insert([
            'user_id' => $user->id,
            'name' => $name,
            'description' => $description,
            'amount' => $amount,
            'isIncome' => $isIncome,
            'created_at' => $created_at
        ]);

        $record_id = $budget->lastInsertId();

        if ($result) {

            $isPaid = DB::table('budgets')->find('id', $record_id);
            $submitted_budget = DB::table('budgets')->find('id', $record_id);

            $text = customTextEditor()->renderNewRecord($submitted_budget);

            $reply_markup = keyboard()->inline()->row(
                Keyboard::inlineButton(['text' => 'پاکش کن', 'callback_data' => "budgetRemove?id={$record_id}"]),
                Keyboard::inlineButton(['text' => $isPaid ? "پرداخت نشده" : "پرداخت شده", 'callback_data' => "budgetUpdatePaid?id={$record_id}"])
            );

            $this->replyWithMessage(['text' => $text, 'reply_markup' => $reply_markup]);
        } else {

            $this->replyWithMessage(['text' => renderText('errorCommon')]);
        }
    }
}
