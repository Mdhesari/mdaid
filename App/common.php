<?php

use App\Application\Application;
use App\Application\MyHelper\CustomTextEditor;
use App\Helper\FileHandler;
use App\Helper\TextEditor;
use App\Model\Replies;
use App\Model\User;
use Telegram\Bot\Keyboard\Keyboard;

if (!function_exists('dd')) {
    function dd($var = null)
    {
        var_dump($var);
        die();
    }
}

if (!function_exists('keyboard')) {

    function keyboard(...$buttons)
    {

        $keyboard = Keyboard::make();

        if (is_string($buttons[0])) {

            switch ($buttons[0]) {
                default:
                    $keyboard->inline()
                        ->row(
                            Keyboard::inlineButton(['text' => 'راهنما', 'callback_data' => 'help']),
                            Keyboard::inlineButton(['text' => 'شروع کار', 'callback_data' => 'init'])
                        );
                    break;
            }
        } else if (count($buttons) > 0) {

            $keyboard = $keyboard->row($buttons);
        }

        return $keyboard;
    }
}

if (!function_exists('fileHandler')) {

    function fileHandler($content = null)
    {

        $fileHanlder = FileHandler::singleton();

        if (!is_null($content)) {

            $fileHanlder->write($content);
        }

        return $fileHanlder;
    }
}

if (!function_exists('testfile')) {

    function testfile($value)
    {

        $file = fopen('test.txt', 'w+');
        fwrite($file, $value);
        fclose($file);
    }
}

if (!function_exists('renderText')) {

    function renderText($name)
    {

        $instance = new Replies;

        $result = $instance->find('name', $name);

        if (!$result)
            return false;

        return $result->text;
    }
}

if (!function_exists('user')) {

    function user($id = null)
    {

        $user = new User();

        if (is_null($id)) {

            return $user;
        }

        return $user->find('id', $id);
    }
}

if (!function_exists('textEditor')) {

    function textEditor($text = null)
    {

        $editor = new TextEditor;

        if (is_null($text)) {

            return $editor;
        }

        return $editor->text($text);
    }
}

if (!function_exists('customTextEditor')) {

    function customTextEditor()
    {

        $editor = new CustomTextEditor;

        return $editor;
    }
}

function renderQueries($query)
{

    $pos = strpos($query, '?');

    if ($pos !== false) {

        $result = [];

        $queries = substr($query, $pos + 1, strlen($query));

        $queries = explode('&', $queries);

        foreach ($queries as $value) {

            $equalSignPos = strpos($value, '=');

            $key = substr($value, 0, $equalSignPos);

            $index_value = substr($value, $equalSignPos + 1, strlen($value));

            $result[$key] = $index_value;
        }

        return $result;
    }

    return false;
}
