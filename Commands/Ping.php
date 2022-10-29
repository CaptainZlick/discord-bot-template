<?php

namespace Commands;

use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;

class Ping extends CommandAbstract {

    public function __construct(Discord $discord) {
        parent::__construct($discord, 'ping', 'pong');
    }

    protected function handle(Interaction $interaction) {
        $interaction->respondWithMessage(MessageBuilder::new()->setContent('Pong!'));
    }

}