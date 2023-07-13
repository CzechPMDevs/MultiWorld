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
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use czechpmdevs\multiworld\util\WorldUtils;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\world\WorldCreationOptions;
use function mt_rand;

class CreateSubCommand extends BaseSubCommand {
	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("name"));
		$this->registerArgument(1, new IntegerArgument("seed", true));
		$this->registerArgument(2, new RawStringArgument("generator", true));

		$this->setPermission("multiworld.command.create");
	}

	/**
	 * @param array<string, mixed> $args
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		/** @var string $name */
		$name = $args["name"];
		/** @var int $seed */
		$seed = $args["seed"] ?? mt_rand();
		/** @var string $generatorName */
		$generatorName = $args["generator"] ?? "normal";

		if(Server::getInstance()->getWorldManager()->isWorldGenerated($name)) {
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "create-exists", [$name]));
			return;
		}

		$generator = WorldUtils::getGeneratorByName($generatorName);
		if($generator === null) {
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "create-gennotexists", [$generatorName]));
			return;
		}

		Server::getInstance()->getWorldManager()->generateWorld($name, WorldCreationOptions::create()
			->setSeed($seed)
			->setGeneratorClass($generator->getGeneratorClass())
		);

		$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "create-done", [$name, (string)$seed, $generatorName]));
	}
}
