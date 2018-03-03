<?php

declare(strict_types=1);

namespace multiworld\command\subcommand;

use multiworld\command\MultiWorldCommand;
use multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;

class ListSubcommand extends MultiWorldCommand implements SubCommand {

    /**
     * ListSubcommand constructor.
     */
    public function __construct(){}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        $msg = LanguageManager::translateMessage("list-done");

        $levels = [];

        foreach (scandir($this->getServer()->getDataPath()."worlds") as $file) {
            if($this->getServer()->isLevelGenerated($file)) {
                $isLoaded = $this->getServer()->isLevelLoaded($file);
                $players = 0;

                if($isLoaded) {
                    $players = count($this->getServer()->getLevelByName($file)->getPlayers());
                }

                $levels[$file] = [$isLoaded, $players];
            }
        }

        $msg = str_replace("%1", "(".count($levels)."):", $msg);

        $sender->sendMessage($msg);

        foreach ($levels as $level => [$loaded, $players]) {
            $loaded = $loaded ? "§aloaded§7" : "§cunloaded§7";
            $sender->sendMessage("§7{$level} > {$loaded} §7players: {$players}");
        }
    }
}