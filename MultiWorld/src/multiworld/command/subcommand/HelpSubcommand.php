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

/**
 * Class HelpSubcommand
 * @package multiworld\command\subcommand
 */
class HelpSubcommand extends MultiWorldCommand implements SubCommand {

    /**
     * HelpSubcommand constructor.
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
            $sender->sendMessage($this->getHelpPage(1));
            return;
        }

        if(!is_numeric($args[0])) {
            $sender->sendMessage($this->getHelpPage(1));
            return;
        }

        $sender->sendMessage($this->getHelpPage((int)$args[0]));
    }

    public function getHelpPage(int $page): string {
        $title = LanguageManager::translateMessage("help");

        $title = str_replace("%page", $page, $title);
        $title = str_replace("%max", "2", $title);

        $text = $title;

        switch ($page) {
            default:
                $text .= MultiWorld::EOL.LanguageManager::translateMessage("help-1");
                $text .= MultiWorld::EOL.LanguageManager::translateMessage("help-2");
                $text .= MultiWorld::EOL.LanguageManager::translateMessage("help-3");
                $text .= MultiWorld::EOL.LanguageManager::translateMessage("help-4");
                $text .= MultiWorld::EOL.LanguageManager::translateMessage("help-5");
                break;

            case 2:
                $text .= MultiWorld::EOL.LanguageManager::translateMessage("help-6");
                break;
        }
        return $text;
    }
}
