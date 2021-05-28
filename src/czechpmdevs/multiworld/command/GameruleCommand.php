<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2020  CzechPMDevs
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
use pocketmine\plugin\Plugin;

class GameruleCommand extends Command implements PluginIdentifiableCommand {

    public function __construct() {
        parent::__construct("gamerule", "Edit level gamerules", null, []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if ($sender->hasPermission("mw.cmd.gamerule")) {
            /** @var MultiWorldCommand $mwCmd */
            $mwCmd = $this->getPlugin()->commands["multiworld"];

            /** @var GameruleSubcommand $subCommand */
            $subCommand = $mwCmd->subcommands["gamerule"];
            $subCommand->executeSub($sender, $args, "gamerule");
        } else {
            $sender->sendMessage(LanguageManager::getMsg($sender, "not-perms"));
        }
    }

    public function getPlugin(): Plugin {
        return MultiWorld::getInstance();
    }
}