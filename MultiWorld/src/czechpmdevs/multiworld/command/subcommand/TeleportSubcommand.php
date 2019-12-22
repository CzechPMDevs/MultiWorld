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

use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;

/**
 * Class TeleportSubcommand
 * @package czechpmdevs\multiworld\command\subcommand
 */
class TeleportSubcommand implements SubCommand {

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        try {
            if(!isset($args[0])) {
                $sender->sendMessage(LanguageManager::getMsg($sender, "teleport-usage"));
                return;
            }

            if(!$this->getServer()->isLevelGenerated($args[0])) {
                $sender->sendMessage(LanguageManager::getMsg($sender, "teleport-levelnotexists", [$args[0]]));
                return;
            }

            if(!$this->getServer()->isLevelLoaded($args[0])) {
                $this->getServer()->loadLevel($args[0]);
            }

            $level = $this->getServer()->getLevelByName($args[0]);

            if(!isset($args[1])) {
                if(!$sender instanceof Player) {
                    $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::getMsg($sender, "teleport-usage"));
                    return;
                }

                $sender->teleport($level->getSafeSpawn());
                $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::getMsg($sender, "teleport-done-1", [$level->getName()]));
                return;
            }

            $player = $this->getServer()->getPlayer($args[1]);

            if((!$player instanceof Player) || !$player->isOnline()) {
                $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::getMsg($sender, "teleport-playernotexists"));
                return;
            }

            $player->teleport($level->getSafeSpawn());

            $player->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($sender, "teleport-done-1", [$level->getName()]));
            $sender->sendMessage(LanguageManager::getMsg($sender, "teleport-done-2", [$level->getName(), $player->getName()]));
            return;
        }
        catch (\Exception $exception) {
            MultiWorld::getInstance()->getLogger()->error("An error occurred while teleporting player between worlds: " . $exception->getMessage() . " (at line: " . $exception->getLine() . " , file: ". $exception->getFile() .")");
        }
    }

    /**
     * @return Server $server
     */
    private function getServer(): Server {
        return Server::getInstance();
    }
}
