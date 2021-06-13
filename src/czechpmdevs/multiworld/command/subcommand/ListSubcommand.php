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

use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use czechpmdevs\multiworld\util\WorldUtils;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use function array_values;
use function count;
use function implode;
use function scandir;

class ListSubcommand implements SubCommand {

    public function executeSub(CommandSender $sender, array $args, string $name): void {
        $levels = [];

        if(!($files = scandir(Server::getInstance()->getDataPath() . "worlds"))) {
            $sender->sendMessage(MultiWorld::getPrefix() . "§cUnable to access /worlds/ directory"); // TODO - translation
            return;
        }

        foreach ($files as $file) {
            if (Server::getInstance()->isLevelGenerated($file)) {
                if(Server::getInstance()->isLevelLoaded($file)) {
                    $levels[$file] = "§7$file > §aLoaded§7, " . count(WorldUtils::getLevelByNameNonNull($file)->getPlayers()) . " Players";
                } else {
                    $level[$file] = "§7$file > §cUnloaded";
                }
            }
        }


        $sender->sendMessage(LanguageManager::getMsg($sender, "list-done", [(string)count($levels)]) . "\n" . implode("\n", array_values($levels)));
    }
}