<?php

namespace App\Application\MyHelper;

use App\Helper\TextEditor;
use Telegram\Bot\Helpers\Emojify;

class CustomTextEditor extends TextEditor
{

    public function renderNewRecord($budget)
    {

        $isPaid_text = $budget->isPaid ? Emojify::text('white_check_mark') : Emojify::text('x');

        $info = textEditor("");

        if (!is_null($budget->name)) {

            $info->join("عنوان : ")->join($budget->name);
        }

        if (!is_null($budget->description)) {

            $info->text("توضیحات : ")->join($budget->description);
        }

        $info = $info->output();


        return $this->text(verta($budget->created_at)->format('%d %B، %Y'))
            ->line()
            ->text($budget->isIncome ? " یک درآمد جدید " : " یک خرج جدید ")
            ->join(renderText('budgetSuccessfulSubmit'))
            ->line()
            ->text("$budget->amount تومن ")
            ->text($info)
            ->line()
            ->text("وضعیت پرداخت : " . $isPaid_text)
            ->output();
    }

    /**
     * renderMultiRecord
     * 
     * Notice :: you are required to use HTML parse_mode
     *
     * @param  mixed $records
     * @param  mixed $text
     *
     * @return void
     */
    public function renderMultiRecord($records, TextEditor $text = null)
    {

        if (is_null($text))
            $text = $this;

        if (count($records) > 0) {
            foreach ($records as $key => $record) {

                $number = (string) $key + 1;

                $arr_number = str_split($number);

                $number = textEditor('');

                array_map(function ($element) use ($number) {

                    $number->join(Emojify::text($element));
                }, $arr_number);

                $number = $number->output();

                $text->text($number);

                if (!is_null($record->name)) {

                    $text->text("عنوان : ")->join($record->name);
                }

                if (!is_null($record->description)) {

                    $text->text("توضیحات : ")->join($record->description);
                }

                $text
                    ->text("نوع : ")->join($record->isIncome ? " درآمد " . Emojify::text("moneybag") : " خرج " . Emojify::text("money_with_wings"))
                    ->text("وضعیت پرداخت : ")->join($record->isPaid ? Emojify::text('white_check_mark') : Emojify::text('x'))
                    ->text("تاریخ ثبت : ")
                    ->join(verta($record->created_at)->format("%d %B, %Y H:i:s"))
                    ->text($record->amount)->join(" تومان")
                    ->line();

                if (isset($records[$key + 1]))
                    $text->text("---------------");
            }
        } else {

            $text->line()->text('هنوز هیچ اطلاعاتی ثبت نشده است.');
        }

        $text->lineAndSpace();

        return $text;
    }
}
