<?php

namespace App\Application\Telegram\Commands\Filters;

use App\Application\Contracts\Filter;
use Carbon\Carbon;
use Telegram\Bot\Helpers\Emojify;

class FilterIncome extends Filter
{

    protected $name = 'filter_income';

    protected $description = 'کل درآمد ها';

    protected $aliases = ['درآمد ها'];

    public function __construct()
    {

        $this->aliases[] = 'درآمد ها ' . Emojify::text('heavy_plus_sign');
    }

    public function filter()
    {

        return [
            'created_at' => Carbon::today()->subWeek(),
            'isIncome' => true
        ];
    }


    public function renderText(): string
    {

        $filters = $this->filter();

        $my_text = textEditor("درآمد های ثبت شده از یک هفته گذشته ")
            ->join(verta($filters['created_at'])->format('%d %B، %Y'))
            ->join(" تا امروز ")
            ->join(verta(Carbon::today())->format('%d %B'))
            ->line();

        $text = customTextEditor()->renderMultiRecord($this->records, $my_text)->output();

        return $text;
    }
}
