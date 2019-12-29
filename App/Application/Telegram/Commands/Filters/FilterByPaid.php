<?php

namespace App\Application\Telegram\Commands\Filters;

use App\Application\Contracts\Filter;
use Carbon\Carbon;

class FilterByPaid extends Filter
{

    protected $name = 'filter_by_paid';

    protected $aliases = ['پرداخت شده ها'];

    protected $description = 'آمار پرداخت شده ها';

    public function filter()
    {

        return [
            'isPaid' => true
        ];
    }

    public function renderText(): string
    {

        $my_text = textEditor("کل اطلاعات ثبت شده از زمان شروع کار با ربات که پرداخت شده اند تا به امروز")
            ->space()->join(verta(Carbon::now())->format('%d %B, %Y'));

        $text = customTextEditor()->renderMultiRecord($this->records, $my_text)->output();

        return $text;
    }
}
