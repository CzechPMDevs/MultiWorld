<?php

declare(strict_types=1);


namespace multiworld;

use multiworld\command\MultiWorldCommand;
use multiworld\generator\ender\EnderGenerator;
use multiworld\generator\void\VoidGenerator;
use multiworld\util\ConfigManager;
use multiworld\util\LanguageManager;
use pocketmine\level\generator\Generator;
use pocketmine\plugin\PluginBase;

/**
 * Class MultiWorld
 * @package multiworld
 */
class MultiWorld extends PluginBase {

    const NAME = "MultiWorld";
    const VERSION = "1.3.3";
    const AUTHOR = "GamakCZ";
    const GITHUB = "https://github.com/CzechPMDevs/multiworld/";

    /** @var  MultiWorld $instance */
    private static $instance;

    /** @var  string $prefix */
    private static $prefix;

    /**
     * @var array $managers
     * @var array $editors
     */
    public $managers = [], $editors = [];

    public function onEnable() {
        self::$instance = $this;
        $this->registerGenerators();
        $this->registerManagers();
        $this->check();
        $this->getServer()->getCommandMap()->register("multiworld", new MultiWorldCommand("multiworld", "multiworld commands", null, ["mw", "wm"]));
        if($this->isEnabled()) {
            $phar = null;
            $this->isPhar() ? $phar = "Phar" : $phar = "src";
            $this->getLogger()->info("\n".
                "§c--------------------------------\n".
                "§6§lCzechPMDevs §r§e>>> §bBuilderTools\n".
                "§o§9MultiWorld ported to PocketMine\n".
                "§aAuthors: §7GamakCZ, Kyd\n".
                "§aVersion: §7".$this->getDescription()->getVersion()."\n".
                "§aStatus: §7Loading...\n".
                "§c--------------------------------");
            if(!in_array(LanguageManager::getLang(), ["Czech", "English"])) {
                $this->getLogger()->notice("Language ".LanguageManager::getLang(). "is not 100% supported. You can fix it on ".self::GITHUB."pulls");
            }
        }
        else {
            $this->getLogger()->info(self::getPrefix()."§6Submit issue to ".self::GITHUB."/issues");
        }

    }

    public function onDisable() {
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
        $return = (($configManager = $this->managers["ConfigManager"]) instanceof ConfigManager) ? $configManager : null;
        if(!$return instanceof ConfigManager) {
            $this->getLogger()->critical("§cCloud not found ConfigManager!");
        }
        return $return;
    }

    /**
     * @return LanguageManager
     */
    public function getLanguageManager():LanguageManager {
        return (($languageManager = $this->managers["LanguageManager"]) instanceof LanguageManager) ? $languageManager : null;
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
            $this->getLogger()->critical(self::getPrefix()."§cDownload plugin form github! (https://github.com/CzechPMDevs/MultiWorld)");
        }
    }
}

