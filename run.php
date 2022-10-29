<?php

include __DIR__.'/vendor/autoload.php';

use Commands\CommandAbstract;
use Discord\Discord;
use Discord\WebSockets\Intents;
use Dotenv\Dotenv;
use React\EventLoop\Loop;


$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$loop = Loop::get();

$discord = new Discord([
    'token' => $_ENV['DISCORD_TOKEN'],
    'intents' => Intents::getDefaultIntents(),
    'loop' => $loop,
]);

$discord->on('ready', function (Discord $discord) {
    CommandAbstract::registerCommands($discord);
    $discord->getLogger()->info("Bot logged in as '{$discord->username}#{$discord->discriminator}' and is ready!");
});

$discord->run();
