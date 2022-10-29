<?php

namespace Commands;

use Closure;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;
use ReflectionClass;

abstract class CommandAbstract {

    protected Discord $discord;

    protected string $name;

    protected string $description;

    public function __construct(Discord $discord, string $name, string $description, array $options = []) {
        $this->discord = $discord;
        $this->name = $name;
        $this->description = $description;
        $this->options = $options;

        $this->register();
        $this->listen(function(Interaction $interaction) {
            $this->handle($interaction);
        });
    }

    public static function registerCommands(Discord $discord)
    {
        $i = 0;

        foreach (scandir(__DIR__) as $commandFile) {
            if (in_array($commandFile, ['.', '..'])) {
                continue;
            }

            $commandName = basename($commandFile, '.php');
            $commandClassName = __NAMESPACE__ . "\\{$commandName}";

            $reflectionClass = new ReflectionClass($commandClassName);
            if (!$reflectionClass->isInstantiable()) {
                continue;
            }

            $i++;
            $discord->getLogger()->info("{$i}. register {$reflectionClass->getShortName()} command.");
            new $commandClassName($discord);
        }
    }

    protected function register() {
        $command = new Command($this->discord, [
            'name' => $this->name,
            'description' => $this->description,
            'options' => $this->options,
        ]);

        if (isset($_ENV['GUILD_ID'])) {
            $guild = $this->discord->guilds->get('id', $_ENV['GUILD_ID']);
            $guild->commands->save($command);
        } else {
            $this->discord->application->commands->save($command);
        }
    }

    protected function listen(Closure $callback) {
        $this->discord->listenCommand($this->name, function (Interaction $interaction) use ($callback) {
            $callback($interaction);
        });
    }

    protected abstract function handle(Interaction $interaction);

}