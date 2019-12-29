<?php

namespace App\Model;

use App\Helper\DB;
use Exception;

class User extends DB
{

    protected $table = "users";

    protected $user_id = null;

    public function __construct($user_id = null)
    {
        parent::__construct();

        $this->user_id = $user_id;
    }

    public function currentState($user_id)
    {

        $state_id = ($this->find('user_id', $user_id))->state_id;

        return $state_id;
    }

    public function save($message)
    {

        $id = $message->getFrom()->getId();

        if (!$this->find('user_id', $id)) {

            return $this->insert(
                [
                    'user_id' => $id,
                    'full_name' => sprintf("%s %s", $message->getFrom()->getFirstName(), $message->getFrom()->getLastName()),
                    'username' => $message->getFrom()->getUsername(),
                    'lang_code' => $message->getFrom()->getLanguageCode(),
                ]
            );
        }

        return false;
    }

    public function setState($type, $user_id = null)
    {

        $state = new State;

        if (is_null($this->user_id) && is_null($user_id)) {

            throw new Exception('User id is empty!');
        }

        $exists = $state->getState($type);

        if ($exists) {
            // can change
            if ($exists->id !== $this->currentState($user_id)) {

                return $this->where('user_id', $user_id)
                    ->update(['state_id' => $exists->id]);
            }
        } else {

            throw new Exception("State does not exist");
        }

        return false;
    }
}
