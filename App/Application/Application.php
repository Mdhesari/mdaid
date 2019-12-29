<?php

namespace App\Application;

use Telegram\Bot\Api;
use App\Helper\DB;
use App\Model\State;
use Telegram\Bot\Objects\Message;

// use Telegram\Bot\Commands\StartCommand;


class Application
{

    protected static $instance = null;

    public $bot;

    protected $config;

    public $update;

    /**
     * setup the application
     *
     * @return void
     */
    public function __construct()
    {

        $this->setup();

        $this->init();
    }

    public static function singleton()
    {

        if (is_null(self::$instance))
            return self::$instance = new Application;

        return self::$instance;
    }

    /**
     * initalize the state
     *
     * @return void
     */
    public function init()
    {

        testfile(print_r($this->update, true));

        $state = $this->renderState();

        (new $state($this))->execute();
    }

    public function renderState()
    {

        $state = State::HOME;

        $user = DB::table('users')->find('user_id', $this->update->getMessage()->getChat()->getId());

        if ($user) {

            $state = (DB::table('states')->find('id', $user->state_id))->name;
        }

        return $state = "\App\Application\State\\" . ucwords($state);
    }

    public function setup()
    {

        $this->setConfig();

        $telegram = $this->config['bot']['telegram'];

        $this->bot = new Api($telegram['token'], $telegram['async']);

        $this->update = $this->bot->getWebhookUpdates();
    }

    public function setConfig()
    {

        $this->config = include CONFIG_PATH;
    }
}
