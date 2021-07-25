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

namespace czechpmdevs\multiworld;

use czechpmdevs\multiworld\command\GameRuleCommand;
use czechpmdevs\multiworld\command\MultiWorldCommand;
use czechpmdevs\multiworld\generator\void\VoidGenerator;
use czechpmdevs\multiworld\level\gamerules\GameRules;
use czechpmdevs\multiworld\util\ConfigManager;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\World;
use pocketmine\plugin\PluginBase;

class MultiWorld extends PluginBase {

    /** @var GameRules[] */
    public static array $gameRules = [];
    /** @var MultiWorld */
    private static MultiWorld $instance;
    /** @var LanguageManager */
    public LanguageManager $languageManager;
    /** @var ConfigManager */
    public ConfigManager $configManager;

    /** @var Command[] */
    public array $commands = [];

    public function onLoad(): void {
        $start = !isset(MultiWorld::$instance);
        MultiWorld::$instance = $this;

        if ($start) {
            $generators = [
                "void" => VoidGenerator::class,
            ];

            foreach ($generators as $name => $class) {
                GeneratorManager::getInstance()->addGenerator($class, $name, true);
            }
        }
    }

    public function onEnable(): void {
        $this->configManager = new ConfigManager();
        $this->languageManager = new LanguageManager();

        $this->commands = [
            "multiworld" => new MultiWorldCommand(),
            "gamerule" => new GameRuleCommand()
        ];

        foreach ($this->commands as $command) {
            $this->getServer()->getCommandMap()->register("MultiWorld", $command);
        }

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    /**
     * @internal
     */
    static function unloadWorld(World $world): void {
        unset(MultiWorld::$gameRules[$world->getId()]);
    }

    public static function getGameRules(World $world): GameRules {
        return MultiWorld::$gameRules[$world->getId()] ?? MultiWorld::$gameRules[$world->getId()] = GameRules::loadFromWorld($world);
    }

    public static function getPrefix(): string {
        return ConfigManager::getPrefix();
    }

    public static function getInstance(): MultiWorld {
        return MultiWorld::$instance;
    }
}
