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

use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;

/**
 * Class HelpSubcommand
 * @package czechpmdevs\multiworld\command\subcommand
 */
class HelpSubcommand implements SubCommand {

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     *
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(!isset($args[0])) {
            $sender->sendMessage($this->getHelpPage($sender, 1));
            return;
        }

        if(!is_numeric($args[0])) {
            $sender->sendMessage($this->getHelpPage($sender,1));
            return;
        }

        $sender->sendMessage($this->getHelpPage($sender, (int)$args[0]));
    }

    /**
     * @param CommandSender $sender
     * @param int $page
     *
     * @return string
     */
    public function getHelpPage(CommandSender $sender, int $page): string {
        $title = LanguageManager::getMsg($sender, "help", [$page, "2"]);

        $text = $title;

        switch ($page) {
            default:
                $text .= "\n" . LanguageManager::getMsg($sender, "help-1");
                $text .= "\n" . LanguageManager::getMsg($sender, "help-2");
                $text .= "\n" . LanguageManager::getMsg($sender, "help-3");
                $text .= "\n" . LanguageManager::getMsg($sender, "help-4");
                $text .= "\n" . LanguageManager::getMsg($sender, "help-5");
                break;

            case 2:
                $text .= "\n" . LanguageManager::getMsg($sender, "help-6");
                $text .= "\n" . LanguageManager::getMsg($sender, "help-7");
                $text .= "\n" . LanguageManager::getMsg($sender, "help-8");
                $text .= "\n" . LanguageManager::getMsg($sender, "help-9");
                $text .= "\n" . LanguageManager::getMsg($sender, "help-10");
                break;
        }
        return $text;
    }
}
