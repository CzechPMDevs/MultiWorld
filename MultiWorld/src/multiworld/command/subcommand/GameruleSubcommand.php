<?php

declare(strict_types=1);

namespace multiworld\command\subcommand;

use multiworld\api\WorldGameRulesAPI;
use multiworld\api\WorldManagementAPI;
use multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class GameruleSubcommand
 * @package multiworld\command\subcommand
 */
class GameruleSubcommand implements SubCommand {

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     *
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(!isset($args[0])) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule.usage"));
            return;
        }

        $all = WorldGameRulesAPI::getAllGameRules();

        if($args[0] == "list") {
            $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule.list", [implode(", ", $all)]));
            return;
        }

        if(!isset($args[1])) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule.usage"));
            return;
        }

        if(!in_array($args[0], $all)) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule.notexists", [$args[0]]));
            return;
        }

        if(!in_array($args[1], ["true", "false"])) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule.usage"));
            return;
        }

        if(!isset($args[2])) {
            if($sender instanceof Player) {
                WorldGameRulesAPI::updateLevelGameRule($sender->getLevel(), $args[0], $args[1] == "true");
                $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule.done", [$args[0], $sender->getLevel(), $args[1]]));
                return;
            }
            else {
                $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule.usage"));
                return;
            }
        }

        if(!WorldManagementAPI::isLevelGenerated($args[2])) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule.levelnotfound", [$args[1]]));
            return;
        }

        WorldGameRulesAPI::updateLevelGameRule(WorldManagementAPI::getLevel($args[1]), $args[0], $args[2] == "true");
        $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule.done", [$args[0], $args[1], $args[2]]));
    }
}