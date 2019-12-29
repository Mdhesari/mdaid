<?php

namespace App\Application\Telegram\Commands\Filters;

use App\Application\Contracts\Filter;
use Carbon\Carbon;
use Telegram\Bot\Helpers\Emojify;

class FilterAll extends Filter
{

    protected $name = 'filter_all';

    protected $aliases = ['کل آمار'];

    protected $description = 'درخواست کل آمار';

    public function __construct()
    {

        $this->aliases[] = 'کل آمار ' . Emojify::text('bar_chart');
    }

    public function filter()
    {

        return [];
    }

    public function renderText(): string
    {

        $my_text = textEditor("کل اطلاعات ثبت شده از زمان شروع کار با ربات تا به امروز")
            ->space()->join(verta(Carbon::now())->format('%d %B, %Y'))
            ->line()
            ->text("============================")->line();

        $text = customTextEditor()->renderMultiRecord($this->records, $my_text)->output();

        return $text;
    }
}
