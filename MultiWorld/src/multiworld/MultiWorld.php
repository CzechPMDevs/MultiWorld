<?php

declare(strict_types=1);

/**
 * 1.4 TODO:
 * - API 3.0.0-ALPHA7 - 3.0.0-ALPHA9
 * - WorldData for gamememode, gamerule etc.
 * - Add simple WorldEdit
 * - Support for 1.2
 * - All move to multiworld\...
 * - Fix languages
 */

namespace multiworld;

use multiworld\command\MultiWorldCommand;
use multiworld\generator\ender\EnderGenerator;
use multiworld\generator\void\VoidGenerator;
use multiworld\util\ConfigManager;
use multiworld\util\LanguageManager;
use multiworld\worldedit\WorldEdit;
use pocketmine\level\generator\Generator;
use pocketmine\plugin\PluginBase;

/**
 * Class multiworld
 * @package multiworld
 */
class MultiWorld extends PluginBase {

    const NAME = "MultiWorld";
    const VERSION = "1.4.0 [BETA 1]";
    const AUTHOR = "GamakCZ";
    const GITHUB = "https://github.com/CzechPMDevs/multiworld/";

    /** @var  MultiWorld $instance */
    static $instance;

    /** @var  string $prefix */
    static $prefix;

    /**
     * @var array $managers
     * @var array $editors
     */
    public $managers = [], $editors = [];

    public function onEnable() {
        self::$instance = $this;
        $this->registerGenerators();
        $this->registerManagers();
        $this->registerEditors();
        $this->check();
        $this->getServer()->getCommandMap()->register("multiworld", new MultiWorldCommand("multiworld", "multiworld commands", null, ["mw", "wm"]));
        if($this->isEnabled()) {
            $phar = null;
            $this->isPhar() ? $phar = "Phar" : $phar = "src";
            $this->getLogger()->info("\n§5**********************************************\n".
                "§6 ---- == §c[§aMultiWorld§c]§6== ----\n".
                "§9> Version: §e{$this->getDescription()->getVersion()}\n".
                "§9> Author: §eCzechPMDevs :: GamakCZ, Kyd\n".
                "§9> GitHub: §e".self::GITHUB."\n".
                "§9> Package: §e{$phar}\n".
                "§9> Language: §e".LanguageManager::getLang()."\n".
                "§5**********************************************");
            if(!in_array(LanguageManager::getLang(), ["Czech", "English"])) {
                $this->getLogger()->notice("Language ".LanguageManager::getLang(). "is not 100% supported. You can fix it on ".self::GITHUB."pulls");
            }
        }
        else {
            $this->getLogger()->info(self::getPrefix()."§6Submit issue to ".self::GITHUB."/issues");
        }

    }

    public function onDisable() {
        $this->getConfigManager()->getDataManager()->saveAllData();
        $this->getLogger()->info("§aMultiWorld is disabled!");
    }

    /**
     * @return MultiWorld
     */
    public static function getInstance():MultiWorld {
        return self::$instance;
    }

    /**
     * @return string
     */
    public static function getPrefix():string {
        return ConfigManager::getPrefix();
    }

    /**
     * @return ConfigManager
     */
    public function getConfigManager():ConfigManager {
        return (($configManager = $this->managers["ConfigManager"]) instanceof ConfigManager) ? $configManager : null;
    }

    /**
     * @return LanguageManager
     */
    public function getLanguageManager():LanguageManager {
        return (($languageManager = $this->managers["LanguageManager"]) instanceof LanguageManager) ? $languageManager : null;
    }

    /**
     * @return WorldEdit $worldEdit
     */
    public function getWorldEdit():WorldEdit {
        return (($worldEdit = $this->editors["WorldEdit"]) instanceof WorldEdit) ? $worldEdit : null;
    }

    public function registerEditors() {
        $this->editors["WorldEdit"] = new WorldEdit($this);
    }

    public function registerManagers() {
        $this->managers["ConfigManager"] = new ConfigManager($this);
        $this->managers["LanguageManager"] = new LanguageManager($this);
    }

    public function registerGenerators() {
        Generator::addGenerator(EnderGenerator::class, "ender");
        Generator::addGenerator(VoidGenerator::class, "void");
    }

    public function check() {
        if($this->getServer()->getName() != "PocketMine-MP") {
            $this->getLogger()->critical("§cMultiWorld does not support {$this->getServer()->getName()}");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }

        if(strval($this->getDescription()->getName()) != self::NAME || strval($this->getDescription()->getVersion()) != self::VERSION) {
            $this->getServer()->getPluginManager()->disablePlugin($this);
            $this->getLogger()->critical(self::getPrefix()."§cDownload plugin form github! (https://github.com/CzechPMDevs/multiworld)");
        }
    }
}

