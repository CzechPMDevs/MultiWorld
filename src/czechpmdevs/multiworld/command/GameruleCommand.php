<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2021  CzechPMDevs
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

namespace czechpmdevs\multiworld\command;

use czechpmdevs\multiworld\command\subcommand\GameruleSubcommand;
use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use function implode;
use function in_array;
use function strtolower;

class GameruleCommand extends Command implements PluginIdentifiableCommand {

    public function __construct() {
        parent::__construct("gamerule", "Edit level gamerules", null, []);
        $this->setPermission("multiworld.command.gamerule");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->testPermission($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule-usage"));
            return;
        }

        foreach ($all as $index => $string) {
            $all[$index] = strtolower($string);
        }

        if (!isset($args[1])) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule-usage"));
            return;
        }

        if (!in_array(strtolower($args[0]), $all)) {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($sender, "gamerule-notexists", [$args[0]]));
            return;
        }

        if (!in_array($args[1], ["true", "false"])) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule-usage"));
            return;
        }

        if (!isset($args[2])) {
            if ($sender instanceof Player) {
                WorldGameRulesAPI::updateLevelGameRule($sender->getLevel(), WorldGameRulesAPI::getRuleFromLowerString($args[0]), $args[1] == "true");
                $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($sender, "gamerule-done", [$args[0], $sender->getLevel()->getFolderName(), $args[1]]));
                return;
            } else {
                $sender->sendMessage(LanguageManager::getMsg($sender, "gamerule-usage"));
                return;
            }
        }

        if (!WorldUtils::isLevelGenerated($args[2]) || WorldUtils::getLevel($args[1]) === null) {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($sender, "gamerule-levelnotfound", [$args[1]]));
            return;
        }

        WorldGameRulesAPI::updateLevelGameRule(WorldUtils::getLevel($args[1]), WorldGameRulesAPI::getRuleFromLowerString($args[0]), $args[2] == "true");
        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($sender, "gamerule-done", [$args[0], $args[1], $args[2]]));
    }

    /**
     * @return MultiWorld
     */
    public function getPlugin(): Plugin {
        return MultiWorld::getInstance();
    }
}