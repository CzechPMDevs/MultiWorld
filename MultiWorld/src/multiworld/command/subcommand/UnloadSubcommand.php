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

/**
 * Class UnloadSubcommand
 * @package multiworld\command\subcommand
 */
class UnloadSubcommand extends MultiWorldCommand implements SubCommand {

    /**
     * UnloadSubcommand constructor.
     */
    public function __construct() {}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     *
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if (empty($args[0])) {
            $sender->sendMessage(LanguageManager::translateMessage("unload-usage"));
            return;
        }

        if(!$this->getServer()->isLevelGenerated($args[0])) {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("unload-levelnotexists"));
            return;
        }

        if(!$this->getServer()->isLevelLoaded($args[0])) {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("unload-unloaded"));
            return;
        }

        $this->getServer()->unloadLevel($this->getServer()->getLevelByName($args[0]));
        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("unload-done"));
        return;
    }
}
