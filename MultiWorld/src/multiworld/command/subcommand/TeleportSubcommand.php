<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018  CzechPMDevs
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

namespace multiworld\command\subcommand;

use multiworld\command\MultiWorldCommand;
use multiworld\MultiWorld;
use multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class TeleportSubcommand
 * @package multiworld\command\subcommand
 */
class TeleportSubcommand extends MultiWorldCommand implements SubCommand {

    /**
     * TeleportSubcommand constructor.
     */
    public function __construct() {}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(empty($args[0])) {
            LanguageManager::translateMessage("teleport-usage");
            return;
        }

        if(!$this->getServer()->isLevelGenerated($args[0])) {
            $sender->sendMessage(LanguageManager::translateMessage("teleport-levelnotexists"));
            return;
        }

        if(!$this->getServer()->isLevelLoaded($args[0])) {
            $this->getServer()->loadLevel($args[0]);
        }

        $level = $this->getServer()->getLevelByName($args[0]);

        if(empty($args[1])) {
            if(!$sender instanceof Player) {
                $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("teleport-usage"));
                return;
            }

            $sender->teleport($level->getSafeSpawn());

            $msg = LanguageManager::translateMessage("teleport-done-1");
            $msg = str_replace("%1", $level->getName(), $msg);

            $sender->sendMessage(MultiWorld::getPrefix().$msg);
            return;
        }

        $player = $this->getServer()->getPlayer($args[2]);

        if((!$player instanceof Player) || !$player->isOnline()) {

            $msg = LanguageManager::translateMessage("teleport-playernotexists");
            str_replace("%1", $player->getName(), $msg);

            $sender->sendMessage(MultiWorld::getPrefix().$msg);
            return;
        }

        $player->teleport($level->getSafeSpawn());

        $playerMsg = LanguageManager::translateMessage("teleport-done-1");
        str_replace("%1", $level->getName(), $playerMsg);
        $player->sendMessage($playerMsg);

        $msg = LanguageManager::translateMessage("teleport-done-2");
        str_replace("%1", $level->getName(), $msg);
        str_replace("%2", $player->getName(), $msg);
        $player->sendMessage($playerMsg);
        return;
    }
}
