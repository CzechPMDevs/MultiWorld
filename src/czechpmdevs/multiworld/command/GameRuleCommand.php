<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2022  CzechPMDevs
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

use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use czechpmdevs\multiworld\util\WorldUtils;
use czechpmdevs\multiworld\world\gamerules\GameRule;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use function array_combine;
use function array_key_exists;
use function array_keys;
use function array_map;
use function gettype;
use function implode;
use function is_bool;
use function is_float;
use function is_int;
use function json_decode;
use function strtolower;

class GameRuleCommand extends Command implements PluginOwned {

	public function __construct() {
		parent::__construct("gamerule", "Edit world gamerules", null, []);
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

		/** @var GameRule[] $gameRules */
		$gameRules = array_combine(
			array_map(fn(GameRule $rule) => $rule->getRuleName(), GameRule::getAll()),
			GameRule::getAll()
		);

		if($args[0] == "list") {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "gamerule-list", [implode(", ", array_keys($gameRules))]));
			return;
		}

		if((!isset($args[1])) || ((!isset($args[2]) && (!$sender instanceof Player)))) {
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

		$rule = $gameRules[$args[0]];
		$value = json_decode($args[1]);
		if(gettype($rule->getValue()) != gettype($value) || (!is_bool($value) && !is_int($value) && !is_float($value))) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "gamerule-usage"));
			return;
		}

		if(isset($args[2])) {
			$world = WorldUtils::getLoadedWorldByName($args[2]);
			if($world === null) {
				$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "gamerule-levelnotfound", [$args[2]]));
				return;
			}

			MultiWorld::getGameRules($world)->setRule($rule->setValue($value));
			MultiWorld::getGameRules($world)->applyToWorld($world);
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "gamerule-done", [$args[0], $world->getDisplayName(), $args[1]]));
			return;
		}

		/** @var Player $sender */
		MultiWorld::getGameRules($sender->getWorld())->setRule($rule->setValue($value));
		MultiWorld::getGameRules($sender->getWorld())->applyToWorld($sender->getWorld());

		$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "gamerule-done", [$args[0], $sender->getWorld()->getDisplayName(), $args[1]]));
	}

	public function getOwningPlugin(): Plugin {
		return MultiWorld::getInstance();
	}
}