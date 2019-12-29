<?php

namespace App\Application\State;

use App\Application\Application;
use App\Application\Contracts\StateInterface;
use Telegram\Bot\Answers\Answerable;
use Telegram\Bot\Types;

abstract class BaseState implements StateInterface
{

    use Answerable;

    protected $app = null;

    protected static $name = null;

    public function __construct(Application $app)
    {

        $this->app = $app;
        $this->update = $app->update;
        $this->telegram = $app->bot;
    }

    public function execute()
    {

        $this->telegram->commandsHandler(true);

        if ($this->update->isType(Types::CALLBACK_QUERY)) {

            $data = $this->update->getCallbackQuery()->getData();
        } else {

            $data = $this->update->getMessage()->getText();
        }

        return $this->telegram->triggerCommand($data, $this->update);
    }

    protected function notifyError($text = null)
    {

        if (is_null($text)) {

            $text = 'Error occured, On ' . __METHOD__;
        }

        fileHandler()->setSource('compileErrors.txt')
            ->edit($text);

        $this->replyWithMessage(['text' => renderText('errorCommon')]);
    }

    public static function getName()
    {

        static::$name = self::setName();

        return self::$name;
    }

    public static function setName($name = null)
    {

        if (is_null(self::$name) || !is_null($name)) {

            if (is_null($name)) {
                $class_name = \explode('\\', \get_called_class());

                return self::$name = strtolower($class_name[count($class_name) - 1]);
            }

            self::$name = $name;
        }

        return false;
    }
}
