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

namespace czechpmdevs\multiworld;

use czechpmdevs\multiworld\api\WorldManagementAPI;
use czechpmdevs\multiworld\command\GameruleCommand;
use czechpmdevs\multiworld\command\MultiWorldCommand;
use czechpmdevs\multiworld\generator\ender\EnderGenerator;
use czechpmdevs\multiworld\generator\nether\NetherGenerator;
use czechpmdevs\multiworld\generator\normal\BiomeManager;
use czechpmdevs\multiworld\generator\normal\NormalGenerator;
use czechpmdevs\multiworld\generator\skyblock\SkyBlockGenerator;
use czechpmdevs\multiworld\generator\void\VoidGenerator;
use czechpmdevs\multiworld\util\ConfigManager;
use czechpmdevs\multiworld\util\FormManager;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\utils\Random;

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

    /** @var FormManager $formManager */
    public $formManager;

    /** @var Command[] $commands */
    public $commands = [];
    
    public function onLoad() {
        $start = (bool) !(self::$instance instanceof $this);
        self::$instance = $this;

        if($start) {
            $generators = [
                "ender" => EnderGenerator::class,
                "void" => VoidGenerator::class,
                "skyblock" => SkyBlockGenerator::class,
                "nether" => NetherGenerator::class,
                "normal_mw" => NormalGenerator::class
            ];

            foreach ($generators as $name => $class) {
                GeneratorManager::addGenerator($class, $name, true);
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function onEnable() {
        $this->configManager = new ConfigManager($this);
        $this->languageManager = new LanguageManager($this);
        $this->formManager = new FormManager($this);

        $this->commands = [
            "multiworld" => $cmd = new MultiWorldCommand(),
            "gamerule" => new GameruleCommand()
        ];

        foreach ($this->commands as $command) {
            $this->getServer()->getCommandMap()->register("MultiWorld", $command);
        }

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this, $cmd), $this);
        $this->test();
    }

    private function test() {
        if(WorldManagementAPI::isLevelGenerated("Test")) {
            WorldManagementAPI::removeLevel("Test");
        }
        WorldManagementAPI::generateLevel("Test", rand(0, 100), WorldManagementAPI::GENERATOR_NORMAL_CUSTOM);
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
