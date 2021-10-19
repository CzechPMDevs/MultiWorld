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

use czechpmdevs\libpmform\response\FormResponse;
use czechpmdevs\libpmform\type\CustomForm;
use czechpmdevs\libpmform\type\SimpleForm;
use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use czechpmdevs\multiworld\util\WorldUtils;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_shift;
use function array_values;
use function is_array;
use function is_bool;
use function is_numeric;
use function time;
use function trim;

class ManageSubCommand implements SubCommand {

	public function execute(CommandSender $sender, array $args, string $name): void {
		if(!$sender instanceof Player) {
			$sender->sendMessage("§cThis command can be used only in-game!");
			return;
		}

		$form = new SimpleForm("World Manager", "Select action", true);
		$form->addButton("Create a new world");
		$form->addButton("Delete world");
		$form->addButton("Update world game rules");
		$form->addButton("Show world info");
		$form->addButton("Load world");
		$form->addButton("Unload world");
		$form->addButton("Teleport to the world");
		$form->addButton("Teleport player to the world");

		$form->setCallback(static function(Player $player, FormResponse $response): void {
			$customForm = new CustomForm("World Manager");

			switch($response->getData()) {
				case 0:
					$customForm->addLabel("Create world");
					$customForm->addInput("Level name");
					$customForm->addInput("Level seed");
					$customForm->addDropdown("Generator", $generators = ["Normal", "Custom", "Nether", "End", "Flat", "Void", "SkyBlock"]);

					$customForm->setCallback(static function(Player $player, FormResponse $response) use ($generators): void {
						$data = $response->getData();
						if(!is_array($data) || $data[1] === "" || ($data[2] != "" && !is_numeric($data[2])) || !isset($generators[$data[3]])) {
							$player->sendMessage(LanguageManager::translateMessage($player, "forms-invalid"));
							return;
						}

						$name = $data[1];
						$seed = trim($data[2]) == "" ? time() : (int)$data[2];

						$cmd = "mw create \"$name\" \"$seed\" \"{$generators[$data[3]]}\"";
						Server::getInstance()->dispatchCommand($player, $cmd);
					});

					$player->sendForm($customForm);
					break;
				case 1:
					$customForm->addLabel("Remove world");
					$customForm->addDropdown("Level name", $worlds = WorldUtils::getAllLevels());

					$customForm->setCallback(static function(Player $player, FormResponse $response) use ($worlds): void {
						$data = $response->getData();
						if(!is_array($data) || $data[1] === "") {
							$player->sendMessage(LanguageManager::translateMessage($player, "forms-invalid"));
							return;
						}

						Server::getInstance()->dispatchCommand($player, "mw delete \"{$worlds[$data[1]]}\"");
					});

					$player->sendForm($customForm);
					break;

				case 2:
					$customForm->addLabel("Update level GameRules");
					$rules = MultiWorld::getGameRules($player->getLevelNonNull())->getGameRules();
					foreach($rules as $rule => $value) {
						if(is_bool($value)) {
							$customForm->addToggle($rule, $value);
						} else {
							$customForm->addInput($rule, (string)$value);
						}
					}

					$customForm->setCallback(static function(Player $player, FormResponse $response) use ($rules): void {
						$data = $response->getData();
						if(!is_array($data)) {
							$player->sendMessage(LanguageManager::translateMessage($player, "forms-invalid"));
							return;
						}

						array_shift($data); // There's only label at index 0, we need to have first rule here.

						$gameRules = array_keys($rules);
						foreach($data as $index => $value) {
							if(!array_key_exists($index, $gameRules)) {
								continue;
							}

							$newValue = is_bool($value) ? $value : (int)$value;
							if($rules[$gameRules[$index]] == $newValue) {
								continue;
							}

							$value = is_bool($value) ? ($value ? "true" : "false") : $value;
							Server::getInstance()->dispatchCommand($player, "gamerule $gameRules[$index] $value");
						}
					});

					$player->sendForm($customForm);
					break;

				case 3:
					$customForm->addLabel("Get information about the level");
					$customForm->addDropdown("Levels", $worlds = WorldUtils::getAllLevels());

					$customForm->setCallback(static function(Player $player, FormResponse $response) use ($worlds): void {
						$data = $response->getData();
						if(!is_array($data) || !isset($data[1])) {
							$player->sendMessage(LanguageManager::translateMessage($player, "forms-invalid"));
							return;
						}

						Server::getInstance()->dispatchCommand($player, "mw info \"{$worlds[$data[1]]}\"");
					});

					$player->sendForm($customForm);
					break;

				case 4:
					$customForm->addLabel("Load world");
					$customForm->addDropdown("Level to load", $worlds = array_values(array_filter(WorldUtils::getAllLevels(), fn(string $worldName) => !Server::getInstance()->isLevelLoaded($worldName))));

					$customForm->setCallback(static function(Player $player, FormResponse $response) use ($worlds): void {
						$data = $response->getData();
						if(!is_array($data)) {
							$player->sendMessage(LanguageManager::translateMessage($player, "forms-invalid"));
							return;
						}

						if(!isset($worlds[$data[1]])) {
							$player->sendMessage(LanguageManager::translateMessage($player, "forms-invalid"));
							return;
						}

						Server::getInstance()->dispatchCommand($player, "mw load \"{$worlds[$data[1]]}\"");
					});

					$player->sendForm($customForm);
					break;

				case 5:
					$customForm->addLabel("Unload world");
					$customForm->addDropdown("Level to unload", $worlds = array_values(array_filter(WorldUtils::getAllLevels(), fn(string $worldName) => Server::getInstance()->isLevelLoaded($worldName))));

					$customForm->setCallback(static function(Player $player, FormResponse $response) use ($worlds): void {
						$data = $response->getData();
						if(!is_array($data)) {
							$player->sendMessage(LanguageManager::translateMessage($player, "forms-invalid"));
							return;
						}

						if(!isset($worlds[$data[1]])) {
							$player->sendMessage(LanguageManager::translateMessage($player, "forms-invalid"));
							return;
						}

						Server::getInstance()->dispatchCommand($player, "mw unload {$worlds[$data[1]]}");
					});

					$player->sendForm($customForm);
					break;

				case 6:
					$customForm->addLabel("Teleport to level");
					$customForm->addDropdown("Level", $worlds = WorldUtils::getAllLevels());

					$customForm->setCallback(static function(Player $player, FormResponse $response) use ($worlds): void {
						$data = $response->getData();
						if(!is_array($data)) {
							$player->sendMessage(LanguageManager::translateMessage($player, "forms-invalid"));
							return;
						}

						if(!isset($worlds[$data[1]])) {
							$player->sendMessage(LanguageManager::translateMessage($player, "forms-invalid"));
							return;
						}

						Server::getInstance()->dispatchCommand($player, "mw teleport \"{$worlds[$data[1]]}\"");
					});

					$player->sendForm($customForm);
					break;

				case 7:
					$customForm->addLabel("Teleport player to level");
					$customForm->addDropdown("Player", $players = array_values(array_map(fn(Player $player) => $player->getName(), Server::getInstance()->getOnlinePlayers())));
					$customForm->addDropdown("Level", $worlds = WorldUtils::getAllLevels());

					$customForm->setCallback(static function(Player $player, FormResponse $response) use ($players, $worlds) {
						$data = $response->getData();
						if(!is_array($data)) {
							$player->sendMessage(LanguageManager::translateMessage($player, "forms-invalid"));
							return;
						}

						if(!isset($players[$data[1]]) || !isset($worlds[$data[2]])) {
							$player->sendMessage(LanguageManager::translateMessage($player, "forms-invalid"));
							return;
						}

						Server::getInstance()->dispatchCommand($player, "mw teleport \"{$worlds[$data[2]]}\" \"{$players[$data[1]]}\"");
					});

					$player->sendForm($customForm);
					break;
			}
		});

		$sender->sendForm($form);
	}
}