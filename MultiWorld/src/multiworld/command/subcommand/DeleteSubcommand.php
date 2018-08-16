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

use multiworld\api\WorldManagementAPI;
use multiworld\MultiWorld;
use multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Server;

/**
 * Class DeleteSubcommand
 * @package multiworld\command\subcommand
 */
class DeleteSubcommand implements SubCommand {

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     *
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if (empty($args[0])) {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($sender, "delete-usage"));
            return;
        }
        if (!$this->getServer()->isLevelGenerated($args[0]) || !file_exists($this->getServer()->getDataPath() . "worlds/{$args[0]}")) {
            $sender->sendMessage(MultiWorld::getPrefix() . str_replace("%1", $args[0], LanguageManager::getMsg($sender, "delete-levelnotexists")));
            return;
        }

        if (!$this->getServer()->getDefaultLevel()->getFolderName() == $this->getServer()->getLevelByName($args[0])->getFolderName()) {
            $sender->sendMessage("§cCould not remove default level!");
            return;
        }

        $files = WorldManagementAPI::removeLevel($args[0]);

        $msg = LanguageManager::getMsg($sender, "delete-done");
        $msg = str_replace("%1", $files, $msg);

        $sender->sendMessage(MultiWorld::getPrefix() . $msg);
    }

    /**
     * @return Server $server
     */
    private function getServer(): Server {
        return Server::getInstance();
    }
}
