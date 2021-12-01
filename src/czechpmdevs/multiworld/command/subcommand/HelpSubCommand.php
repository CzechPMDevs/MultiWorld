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
use function is_numeric;

class HelpSubCommand implements SubCommand {

	public function execute(CommandSender $sender, array $args, string $name): void {
		if(!isset($args[0])) {
			$sender->sendMessage($this->getHelpMessage($sender, 1));
			return;
		}

		if(!is_numeric($args[0])) {
			$sender->sendMessage($this->getHelpMessage($sender, 1));
			return;
		}

		$sender->sendMessage($this->getHelpMessage($sender, (int)$args[0]));
	}

	public function getHelpMessage(CommandSender $sender, int $page): string {
		if($page < 1 || $page > 3) {
			return $this->getHelpMessage($sender, 1);
		}

		$message = LanguageManager::translateMessage($sender, "help", [(string)$page, "3"]);
		for($i = $j = (($page - 1) * 5) + 1, $j = $j + 5; $i < $j; ++$i) {
			$message .= "\n" . LanguageManager::translateMessage($sender, "help-$i");
		}
		return $message;
	}
}
