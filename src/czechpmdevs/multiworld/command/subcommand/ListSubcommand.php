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

namespace czechpmdevs\multiworld\command\subcommand;

use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class ListSubcommand implements SubCommand {

    public function executeSub(CommandSender $sender, array $args, string $name): void {
        $levels = [];

        foreach (scandir($this->getServer()->getDataPath() . "worlds") as $file) {
            if (WorldUtils::isLevelGenerated($file)) {
                $isLoaded = WorldUtils::isLevelLoaded($file);
                $players = 0;

                if ($isLoaded) {
                    $players = count($this->getServer()->getLevelByName($file)->getPlayers());
                }

                $levels[$file] = [$isLoaded, $players];
            }
        }


        $sender->sendMessage(LanguageManager::getMsg($sender, "list-done", [(string)count($levels)]));

        foreach ($levels as $level => [$loaded, $players]) {
            $loaded = $loaded ? "§aloaded§7" : "§cunloaded§7";
            $sender->sendMessage("§7{$level} > {$loaded} §7players: {$players}");
        }
    }

    private function getServer(): Server {
        return Server::getInstance();
    }
}