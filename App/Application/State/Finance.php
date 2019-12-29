<?php

namespace App\Application\State;

use App\Application\Telegram\Commands\Back;
use App\Application\Telegram\Commands\Init;
use App\Application\Telegram\Commands\Filters\FilterAll;
use App\Application\Telegram\Commands\Filters\FilterByPaid;
use App\Application\Telegram\Commands\Filters\FilterByUnpaid;
use App\Application\Telegram\Commands\Filters\FilterExpense;
use App\Application\Telegram\Commands\Filters\FilterIncome;
use Telegram\Bot\Types;

class Finance extends BaseState
{

    public function execute()
    {

        $this->telegram->addCommands([
            Back::class,
            Init::class,
            FilterIncome::class,
            FilterExpense::class,
            FilterAll::class,
            FilterByPaid::class,
            FilterByUnpaid::class
        ]);

        if (parent::execute() !== false) {

            return;
        }

        if ($this->update->isType(Types::CALLBACK_QUERY)) {

            $callback_query = $this->update->getCallbackQuery();

            $data = strtolower($callback_query->getData());

            $callback_id = $callback_query->getId();

            $request = renderQueries($data);

            if (str_contains($data, 'filter')) {

                $filter_command = "App\Application\Telegram\Commands\Filters\\" . $request['type'];

                fileHandler()->varDump($filter_command);

                $filterInstance = new $filter_command;

                $page = (int) $request['page'];

                $page_limit = textEditor($filter_command::PAGINATION . ", ");

                if (str_contains($data, 'nextpage')) {

                    $page_limit->join($page + $filter_command::PAGINATION);
                } else if (str_contains($data, 'prevpage')) {

                    $page_limit->join($page - $filter_command::PAGINATION);
                }

                $page_limit = $page_limit->output();

                $filterInstance->setLimits($page_limit);

                $filterInstance->isEditedMessage();

                $this->telegram->triggerCommand($filterInstance, $this->update);
            }

            return 1;
        }

        $this->replyWithMessage(['text' => renderText('invalidCommand')]);
    }
}
