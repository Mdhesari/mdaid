<?php

namespace App\Application\Telegram\Commands\Filters;

use App\Application\Contracts\Filter;
use Carbon\Carbon;

class FilterByUnpaid extends Filter
{

    protected $name = 'filter_by_unpaid';

    protected $aliases = ['پرداخت نشده ها'];

    protected $description = 'آمار پرداخت نشده ها';

    public function filter()
    {

        return [
            'isPaid' => false
        ];
    }

    public function renderText(): string
    {

        $my_text = textEditor("کل اطلاعات ثبت شده از زمان شروع کار با ربات که پرداخت نشده اند تا به امروز")
            ->space()->join(verta(Carbon::now())->format('%d %B, %Y'));

        $text = customTextEditor()->renderMultiRecord($this->records, $my_text)->output();

        return $text;
    }
}
