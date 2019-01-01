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

use czechpmdevs\multiworld\api\WorldManagementAPI;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Server;

/**
 * Class ListSubcommand
 * @package czechpmdevs\multiworld\command\subcommand
 */
class ListSubcommand implements SubCommand {

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        $levels = [];

        foreach (scandir($this->getServer()->getDataPath()."worlds") as $file) {
            if(WorldManagementAPI::isLevelGenerated($file)) {
                $isLoaded = WorldManagementAPI::isLevelLoaded($file);
                $players = 0;

                if($isLoaded) {
                    $players = count($this->getServer()->getLevelByName($file)->getPlayers());
                }

                $levels[$file] = [$isLoaded, $players];
            }
        }



        $sender->sendMessage(LanguageManager::getMsg($sender, "list-done", [(string) count($levels)]));

        foreach ($levels as $level => [$loaded, $players]) {
            $loaded = $loaded ? "§aloaded§7" : "§cunloaded§7";
            $sender->sendMessage("§7{$level} > {$loaded} §7players: {$players}");
        }
    }

    /**
     * @return Server $server
     */
    private function getServer(): Server {
        return Server::getInstance();
    }
}