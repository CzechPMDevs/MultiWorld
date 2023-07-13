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

namespace czechpmdevs\multiworld;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use czechpmdevs\multiworld\command\MultiWorldCommand;
use czechpmdevs\multiworld\generator\ender\EnderGenerator;
use czechpmdevs\multiworld\generator\skyblock\SkyBlockGenerator;
use czechpmdevs\multiworld\generator\void\VoidGenerator;
use czechpmdevs\multiworld\util\ConfigManager;
use czechpmdevs\multiworld\util\LanguageManager;
use muqsit\vanillagenerator\generator\nether\NetherGenerator;
use muqsit\vanillagenerator\generator\overworld\OverworldGenerator;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\world\generator\GeneratorManager;

class MultiWorld extends PluginBase {
	private static MultiWorld $instance;

	public LanguageManager $languageManager;
	public ConfigManager $configManager;

	/** @var Command[] */
	public array $commands = [];

	public function onLoad(): void {
		MultiWorld::$instance = $this;

		$generators = [
			"ender" => EnderGenerator::class,
			"void" => VoidGenerator::class,
			"skyblock" => SkyBlockGenerator::class,
			"vanilla_normal" => OverworldGenerator::class,
			"vanilla_nether" => NetherGenerator::class
		];
		foreach($generators as $name => $class) {
			GeneratorManager::getInstance()->addGenerator($class, $name, fn() => null, true);
		}
	}

	public function onEnable(): void {
		$this->configManager = new ConfigManager();
		$this->configManager->load();

		$this->languageManager = new LanguageManager();

		$this->commands = [
			"multiworld" => new MultiWorldCommand($this, "multiworld", "MultiWorld commands", ["mw", "wm"]),
		];

		foreach($this->commands as $command) {
			$this->getServer()->getCommandMap()->register("MultiWorld", $command);
		}

		try {
			PacketHooker::register($this);
		} catch(HookAlreadyRegistered) {}
	}

	public static function getPrefix(): string {
		return ConfigManager::getPrefix();
	}

	public static function getInstance(): MultiWorld {
		return MultiWorld::$instance;
	}
}
