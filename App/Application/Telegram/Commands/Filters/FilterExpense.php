<?php

namespace App\Application\Telegram\Commands\Filters;

use App\Application\Contracts\Filter;
use Carbon\Carbon;
use Telegram\Bot\Helpers\Emojify;

class FilterExpense extends Filter
{

    protected $name = 'filter_expense';

    protected $description = 'کل هزینه ها';

    protected $aliases = ['هزینه ها'];

    public function __construct()
    {

        $this->aliases[] = 'هزینه ها ' . Emojify::text('heavy_minus_sign');
    }

    public function filter()
    {

        return array(
            'created_at' => Carbon::today()->subWeek(),
            'isIncome' => false
        );
    }


    public function renderText(): string
    {

        $filters = $this->filter();

        $my_text = textEditor("هزینه های ثبت شده از یک هفته گذشته ")
            ->join(verta($filters['created_at'])->format('%d %B، %Y'))
            ->join(" تا امروز ")
            ->join(verta(Carbon::today())->format('%d %B'))
            ->line();

        $text = customTextEditor()->renderMultiRecord($this->records, $my_text)->output();

        return $text;
    }
}
