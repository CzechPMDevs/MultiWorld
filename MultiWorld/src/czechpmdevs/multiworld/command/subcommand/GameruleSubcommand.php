<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2019  CzechPMDevs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace czechpmdevs\multiworld\command\subcommand;

use czechpmdevs\multiworld\api\WorldGameRulesAPI;
use czechpmdevs\multiworld\api\WorldManagementAPI;
use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class GameruleSubcommand
 * @package czechpmdevs\multiworld\command\subcommand
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
            $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule-usage"));
            return;
        }

        $all = WorldGameRulesAPI::getAllGameRules();


        if($args[0] == "list") {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($sender, "gamerule-list", [implode(", ", $all)]));
            return;
        }

        foreach ($all as $index => $string) {
            $all[$index] = strtolower($string);
        }

        if(!isset($args[1])) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule-usage"));
            return;
        }

        if(!in_array(strtolower($args[0]), $all)) {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($sender, "gamerule-notexists", [$args[0]]));
            return;
        }

        if(!in_array($args[1], ["true", "false"])) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule-usage"));
            return;
        }

        if(!isset($args[2])) {
            if($sender instanceof Player) {
                WorldGameRulesAPI::updateLevelGameRule($sender->getLevel(), WorldGameRulesAPI::getRuleFromLowerString($args[0]), $args[1] == "true");
                $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($sender, "gamerule-done", [$args[0], $sender->getLevel()->getFolderName(), $args[1]]));
                return;
            }
            else {
                $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule-usage"));
                return;
            }
        }

        if(!WorldManagementAPI::isLevelGenerated($args[2]) || WorldManagementAPI::getLevel($args[1]) === null) {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($sender, "gamerule-levelnotfound", [$args[1]]));
            return;
        }

        WorldGameRulesAPI::updateLevelGameRule(WorldManagementAPI::getLevel($args[1]), WorldGameRulesAPI::getRuleFromLowerString($args[0]), $args[2] == "true");
        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($sender, "gamerule-done", [$args[0], $args[1], $args[2]]));
    }
}