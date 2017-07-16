<?php

namespace MultiWorld;

use MultiWorld\Command\MultiWorldCommand;
use MultiWorld\Event\EventListener;
use MultiWorld\Generator\AdvancedGenerator;
use MultiWorld\Generator\BasicGenerator;
use MultiWorld\Task\DelayedTask\RegisterGeneratorTask;
use MultiWorld\Util\ConfigManager;
use MultiWorld\Util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

class MultiWorld extends PluginBase {

    const NAME = "MultiWorld";
    const VERSION = "1.3.0 [BETA 3]";
    const AUTHOR = "GamakCZ";
    const GITHUB = "https://github.com/CzechPMDevs/MultiWorld/";

    /** @var  MultiWorld */
    public static $instance;

    // prefix
    public static $prefix;

    ##\
    ### > Events
    ##/

    /** @var  EventListener */
    public $listener;

    ##\
    ### > Utils
    ##/

    /** @var  ConfigManager */
    public $configmgr;

    /** @var  LanguageManager */
    public $langmgr;


    ##\
    ### > Generators
    ##/

    /** @var  BasicGenerator */
    public $bgenerator;

    /** @var  AdvancedGenerator */
    public $agenerator;

    ##\
    ### > Tasks
    ##/

    /** @var  RegisterGeneratorTask */
    public $registerGeneratorTask;

    ##\
    ### > Commands
    ##/

    /** @var  MultiWorldCommand */
    public $multiWorldCommand;

    public function onEnable() {
        // INSTANCE
        self::$instance = $this;

        // events
        $this->listener = new EventListener($this);
        $this->getServer()->getPluginManager()->registerEvents($this->listener, $this);

        // utils
        $this->configmgr = new ConfigManager($this);
        $this->langmgr = new LanguageManager($this);
        $this->configmgr->initConfig();
        $this->langmgr->loadLang();

        // generators
        $this->bgenerator = new BasicGenerator($this);
        $this->agenerator = new AdvancedGenerator($this);

        // commands
        $this->multiWorldCommand = new MultiWorldCommand($this);
        $this->multiWorldCommand->initCommands();

        if (is_file($this->getDataFolder() . "/config.yml")) {
            if (strval($this->getConfig()->get("plugin-version")) != "1.3.0") {
                $this->getServer()->getPluginManager()->disablePlugin($this);
                $this->getLogger()->critical(self::getPrefix() . "§cConfig is old. Delete config to start MultiWorld.");
            }
        }


        if (strval($this->getDescription()->getName()) != self::NAME || strval($this->getDescription()->getVersion()) != self::VERSION) {
            $this->getServer()->getPluginManager()->disablePlugin($this);
            $this->getLogger()->critical(self::getPrefix() . "§cDownload plugin form github! (https://github.com/CzechPMDevs/MultiWorld)");
        }


        if ($this->isEnabled()) {
            if ($this->isPhar()) {
                $this->getLogger()->info("\n§5**********************************************\n" .
                    "§6 ---- == §c[§aMultiWorld§c]§6== ----\n" .
                    "§9> Version: §e{$this->getDescription()->getVersion()}\n" .
                    "§9> Author: §eCzechPMDevs :: GamakCZ, Kyd\n" .
                    "§9> GitHub: §e" . self::GITHUB . "\n" .
                    "§9> Package: §ePhar\n" .
                    "§9> Language: §e" . LanguageManager::getLang() . "\n" .
                    "§5*********************************************");
            } else {
                $this->getLogger()->info("\n§5**********************************************\n" .
                    "§6 ---- == §c[§aMultiWorld§c]§6== ----\n" .
                    "§9> Version: §e{$this->getDescription()->getVersion()}\n" .
                    "§9> Author: §eCzechPMDevs :: GamakCZ, Kyd\n" .
                    "§9> GitHub: §egithub.com/CzechMPDevs/MultiWorld\n" .
                    "§9> Package: §esrc\n" .
                    "§9> Language: §e" . LanguageManager::getLang() . "\n" .
                    "§5*********************************************");
            }
        }
        else {
            $this->getLogger()->info(self::getPrefix() . "§6Submit issue to " . self::GITHUB . "/issues");
        }
    }

    public function onDisable() {
        $this->getLogger()->info("§aMultiWorld is disabled!");
    }

    /**
     * @param CommandSender $sender
     * @param Command $cmd
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
        return self::getMultiWorldCommnad()->onCommand($sender, $cmd, $args);
    }

    /**
     * @return MultiWorldCommand
     */
    public static function getMultiWorldCommnad() {
        return self::getInstance()->multiWorldCommand;
    }

    /**
     * @return EventListener
     */
    public static function getListener() {
        return self::getInstance()->listener;
    }

    /**
     * @return LanguageManager
     */
    public static function getLanguageManager() {
        return self::getInstance()->langmgr;
    }

    /**
     * @return ConfigManager
     */
    public static function getConfigManager() {
        return self::getInstance()->configmgr;
    }

    /**
     * @return BasicGenerator
     */
    public static function getBasicGenerator() {
        return self::getInstance()->bgenerator;
    }

    /**
     * @return AdvancedGenerator
     */
    public static function getAdvancedGenerator() {
        return self::getInstance()->agenerator;
    }

    /**
     * @return MultiWorld
     */
    public static function getInstance() {
        return self::$instance;
    }

    /**
     * @return string
     */
    public static function getPrefix() {
        return ConfigManager::getPrefix();
    }
}
