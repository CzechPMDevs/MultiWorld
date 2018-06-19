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

use multiworld\command\MultiWorldCommand;
use multiworld\generator\ender\EnderGenerator;
use multiworld\generator\skyblock\SkyBlockGenerator;
use multiworld\generator\void\VoidGenerator;
use multiworld\util\ConfigManager;
use multiworld\util\LanguageManager;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\plugin\PluginBase;

/**
 * Class MultiWorld
 * @package multiworld
 */
class MultiWorld extends PluginBase {

    /** @var string EOL */
    public const EOL = "\n";

    /** @var  MultiWorld $instance */
    private static $instance;

    /** @var LanguageManager $languageManager */
    public $languageManager;

    /** @var ConfigManager $configManager */
    public $configManager;

    public function onEnable() {
        self::$instance = $this;

        if(!class_exists(GeneratorManager::class)) {
            Generator::addGenerator(EnderGenerator::class, "ender");
            Generator::addGenerator(VoidGenerator::class, "void");
            Generator::addGenerator(SkyBlockGenerator::class, "skyblock");
        }

        else {
            GeneratorManager::addGenerator(EnderGenerator::class, "ender");
            GeneratorManager::addGenerator(VoidGenerator::class, "void");
            GeneratorManager::addGenerator(SkyBlockGenerator::class, "skyblock");
        }

        $this->getServer()->getCommandMap()->register("MultiWorld", new MultiWorldCommand);

        $this->configManager = new ConfigManager($this);
        $this->languageManager = new LanguageManager($this);

        if($this->isEnabled()) {
            $phar = null;
            $this->isPhar() ? $phar = "Phar" : $phar = "src";
            $this->getLogger()->info("\n".
                "--------------------------------\n".
                "CzechPMDevs >>> MultiWorld\n".
                "MultiWorld ported to PocketMine\n".
                "Authors: GamakCZ, Kyd\n".
                "Version: ".$this->getDescription()->getVersion()."\n".
                "Status: Loading...\n".
                "--------------------------------");
            if(!in_array(LanguageManager::getLang(), ["Czech", "English", "Japanese"])) {
                $this->getLogger()->notice("Language ".LanguageManager::getLang(). " is not 100% supported. You can fix it on https://github.com/MultiWorld/pulls");
            }
        }
        else {
            $this->getLogger()->critical("Submit issue to https://github.com/CzechPMDevs/MultiWorld/issues");
        }

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
