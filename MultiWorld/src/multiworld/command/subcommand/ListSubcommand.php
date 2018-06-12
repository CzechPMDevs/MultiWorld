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