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

use multiworld\MultiWorld;
use multiworld\command\MultiWorldCommand;
use multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\Player;

/**
 * Class InfoSubcommand
 * @package multiworld\command\subcommand
 */
class InfoSubcommand implements SubCommand {

    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }
        $sender->sendMessage($this->getInfoMsg($sender, $sender->getLevel()));
    }

    public function getInfoMsg(CommandSender $sender, Level $level): string {
        $name = $level->getName();
        $folderName = $level->getFolderName();
        $seed = $level->getSeed();
        $players = count($level->getPlayers());
        $generator = $level->getProvider()->getGenerator();
        $time = $level->getTime();

        $msg = LanguageManager::getMsg($sender, "info", [$name]);
        $msg .= "\n".LanguageManager::getMsg($sender, "info-name", [$name]);
        $msg .= "\n".LanguageManager::getMsg($sender, "info-folderName", [$folderName]);
        $msg .= "\n".LanguageManager::getMsg($sender, "info-players", [$players]);
        $msg .= "\n".LanguageManager::getMsg($sender, "info-generator", [$generator]);
        $msg .= "\n".LanguageManager::getMsg($sender, "info-seed", [$seed]);
        $msg .= "\n".LanguageManager::getMsg($sender,"info-time", [$time]);

        return $msg;
    }
}
