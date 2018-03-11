<?php

declare(strict_types=1);


namespace multiworld;

use multiworld\command\MultiWorldCommand;
use multiworld\generator\ender\EnderGenerator;
use multiworld\generator\skyblock\SkyBlockGenerator;
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

        Generator::addGenerator(EnderGenerator::class, "ender");
        Generator::addGenerator(VoidGenerator::class, "void");
        Generator::addGenerator(SkyBlockGenerator::class, "skyblock");

        $this->getServer()->getCommandMap()->register("MultiWorld", new MultiWorldCommand);

        $this->configManager = new ConfigManager($this);
        $this->languageManager = new LanguageManager($this);

        if($this->isEnabled()) {
            $phar = null;
            $this->isPhar() ? $phar = "Phar" : $phar = "src";
            $this->getLogger()->info("\n".
                "§c--------------------------------\n".
                "§6§lCzechPMDevs §r§e>>> §bMultiWorld\n".
                "§o§9MultiWorld ported to PocketMine\n".
                "§aAuthors: §7GamakCZ, Kyd\n".
                "§aVersion: §7".$this->getDescription()->getVersion()."\n".
                "§aStatus: §7Loading...\n".
                "§c--------------------------------");
            if(!in_array(LanguageManager::getLang(), ["Czech", "English", "Japanese"])) {
                $this->getLogger()->notice("Language ".LanguageManager::getLang(). " is not 100% supported. You can fix it on https://github.com/MultiWorld/pulls");
            }
        }
        else {
            $this->getLogger()->critical("§6Submit issue to https://github.com/CzechPMDevs/MultiWorld/issues");
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
