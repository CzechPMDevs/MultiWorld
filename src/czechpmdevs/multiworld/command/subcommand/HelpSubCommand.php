<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2023  CzechPMDevs
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

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;

class HelpSubCommand extends BaseSubCommand {
	protected function prepare(): void {
		$this->registerArgument(0, new IntegerArgument("page", true));

		$this->setPermission("multiworld.command.help");
	}

	/**
	 * @param array<string, mixed> $args
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		/** @var int $page */
		$page = $args["page"] ?? 1;

		$sender->sendMessage($this->getHelpMessage($sender, $page));
	}

	public function getHelpMessage(CommandSender $sender, int $page): string {
		if($page < 1 || $page > 3) {
			$page = 1;
		}

		$message = LanguageManager::translateMessage($sender, "help", [(string)$page, "3"]);
		for($i = $j = (($page - 1) * 5) + 1, $j = $j + 5; $i < $j; ++$i) {
			$message .= "\n" . LanguageManager::translateMessage($sender, "help-$i");
		}
		return $message;
	}


}
