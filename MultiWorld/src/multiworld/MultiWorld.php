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

namespace multiworld;

use multiworld\command\GameruleCommand;
use multiworld\command\MultiWorldCommand;
use multiworld\generator\ender\EnderGenerator;
use multiworld\generator\skyblock\SkyBlockGenerator;
use multiworld\generator\void\VoidGenerator;
use multiworld\util\ConfigManager;
use multiworld\util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\plugin\PluginBase;

/**
 * Class MultiWorld
 * @package multiworld
 */
class MultiWorld extends PluginBase {

    /** @var  MultiWorld $instance */
    private static $instance;

    /** @var LanguageManager $languageManager */
    public $languageManager;

    /** @var ConfigManager $configManager */
    public $configManager;

    /** @var Command[] $commands */
    public $commands = [];

    public function onEnable() {
        $start = (bool) !(self::$instance instanceof $this);
        self::$instance = $this;

        if($start) {
            $generators = [
                "ender" => EnderGenerator::class,
                "void" => VoidGenerator::class,
                "skyblock" => SkyBlockGenerator::class
            ];

            foreach ($generators as $name => $class) {
                GeneratorManager::addGenerator($class, $name, true);
            }
        }

        $this->configManager = new ConfigManager($this);
        $this->languageManager = new LanguageManager($this);

        $this->commands = [
            "multiworld" => new MultiWorldCommand(),
            "gamerule" => new GameruleCommand()
        ];

        foreach ($this->commands as $command) {
            $this->getServer()->getCommandMap()->register("MultiWorld", $command);
        }

        $this->getServer()->getCommandMap()->register("MultiWorld", $cmd = new MultiWorldCommand);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this, $cmd), $this);
    }

    /**
     * @return MultiWorld $plugin
     */
    public static function getInstance(): MultiWorld {
        return self::$instance;
    }

    /**
     * @return string $prefix
     */
    public static function getPrefix(): string {
        return ConfigManager::getPrefix();
    }
}
