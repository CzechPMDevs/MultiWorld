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

namespace czechpmdevs\multiworld\command;

use czechpmdevs\multiworld\level\gamerules\GameRules;
use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use czechpmdevs\multiworld\util\WorldUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use function array_combine;
use function array_key_exists;
use function array_keys;
use function array_map;
use function implode;
use function is_bool;
use function is_numeric;
use function strtolower;

class GameRuleCommand extends Command implements PluginIdentifiableCommand {

	public function __construct() {
		parent::__construct("gamerule", "Edit level gamerules", null, []);
		$this->setPermission("multiworld.command.gamerule");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			return;
		}

		if(!isset($args[0])) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "gamerule-usage"));
			return;
		}

		$gameRules = GameRules::getDefaultGameRules()->getGameRules();
		if($args[0] == "list") {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "gamerule-list", [implode(", ", array_keys($gameRules))]));
			return;
		}

		if((!isset($args[1])) || (!isset($args[2]) && (!$sender instanceof Player))) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "gamerule-usage"));
			return;
		}

		/** @var string[] $gameRulesMap */
		$gameRulesMap = array_combine(
			array_map(fn(string $rule) => strtolower($rule), array_keys($gameRules)),
			array_keys($gameRules)
		);

		if(!array_key_exists($args[0] = $gameRulesMap[strtolower($args[0])] ?? "unknownRule", $gameRules)) {
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "gamerule-notexists", [$args[0]]));
			return;
		}

		/** @var bool|int|null $value */
		$value = $args[1] == "true" ? true : (
		$args[1] == "false" ? false : (
		is_numeric($args[1]) ? (int)$args[1] : null
		)
		);

		if($value === null || GameRules::getPropertyType($value) != GameRules::getPropertyType($gameRules[$args[0]])) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "gamerule-usage"));
			return;
		}

		if(isset($args[2])) {
			$level = WorldUtils::getLoadedLevelByName($args[2]);
			if($level === null) {
				$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "gamerule-levelnotfound", [$args[2]]));
				return;
			}

			if(is_bool($value)) {
				MultiWorld::getGameRules($level)->setBool($args[0], $value);
			} else {
				MultiWorld::getGameRules($level)->setInteger($args[1], (int)$value);
			}

			MultiWorld::getGameRules($level)->applyToLevel($level);
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "gamerule-done", [$args[0], $level->getName(), $args[1]]));
			return;
		}

		/** @var Player $sender */
		if(is_bool($value)) {
			MultiWorld::getGameRules($sender->getLevelNonNull())->setBool($args[0], $value);
		} else {
			MultiWorld::getGameRules($sender->getLevelNonNull())->setInteger($args[0], (int)$value);
		}

		MultiWorld::getGameRules($sender->getLevelNonNull())->applyToLevel($sender->getLevelNonNull());
		$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "gamerule-done", [$args[0], $sender->getLevelNonNull()->getName(), $args[1]]));
	}

	/**
	 * @return MultiWorld
	 */
	public function getPlugin(): Plugin {
		return MultiWorld::getInstance();
	}
}