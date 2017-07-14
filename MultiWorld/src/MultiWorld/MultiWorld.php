<?php

namespace MultiWorld;

use MultiWorld\Event\EventListener;
use MultiWorld\Generator\AdvancedGenerator;
use MultiWorld\Generator\BasicGenerator;
use MultiWorld\Task\DelayedTask\RegisterGeneratorTask;
use MultiWorld\Util\ConfigManager;
use MultiWorld\Util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class MultiWorld extends PluginBase {

    const NAME = "MultiWorld";
    const VERSION = "1.3.0 [BETA 2]";
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

        // tasks
        if(is_file($this->getDataFolder()."/config.yml")) {
            if(strval($this->getConfig()->get("plugin-version")) != "1.3.0") {
                $this->getServer()->getPluginManager()->disablePlugin($this);
                $this->getLogger()->critical(self::getPrefix()."§cConfig is old. Delete config to start MultiWorld.");
            }
        }


        if(strval($this->getDescription()->getName()) != self::NAME || strval($this->getDescription()->getVersion()) != self::VERSION) {
            $this->getServer()->getPluginManager()->disablePlugin($this);
            $this->getLogger()->critical(self::getPrefix()."§cDownload plugin form github! (https://github.com/CzechPMDevs/MultiWorld)");
        }


        if($this->isEnabled()) {
            if($this->isPhar()) {
                $this->getLogger()->info("\n§5******************************************\n".
                    "§6 ---- == §c[§aMultiWorld§c]§6== ----\n".
                    "§9> Version: §e{$this->getDescription()->getVersion()}\n".
                    "§9> Author: §eCzechPMDevs :: GamakCZ, Kyd\n".
                    "§9> GitHub: §e".self::GITHUB."\n".
                    "§9> Package: §ePhar\n".
                    "§9> Language: §e".LanguageManager::getLang()."\n".
                    "§5*****************************************");
            }
            else {
                $this->getLogger()->info("\n§5******************************************\n".
                    "§6 ---- == §c[§aMultiWorld§c]§6== ----\n".
                    "§9> Version: §e{$this->getDescription()->getVersion()}\n".
                    "§9> Author: §eCzechPMDevs :: GamakCZ, Kyd\n".
                    "§9> GitHub: §egithub.com/CzechMPDevs/MultiWorld\n".
                    "§9> Package: §esrc\n".
                    "§9> Language: §e".LanguageManager::getLang()."\n".
                    "§5*****************************************");
            }
        }
        else {
            $this->getLogger()->info(self::getPrefix()."§6Submit issue to ".self::GITHUB."/issues");
        }

    }

    public function onDisable() {
        $this->getLogger()->info("§aMultiWorld is disabled!");
    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
        if(!($sender instanceof Player)) {
            return;
        }
        if($cmd->getName() == "multiworld") {
            if(isset($args[0])) {
                switch (strtolower($args[0])) {
                    case "help":
                    case "?":
                        if(!$sender->hasPermission("mw.cmd.help")) {
                            $sender->sendMessage("§cYou have not permissions to use this command!");
                            break;
                        }
                        $sender->sendMessage(LanguageManager::translateMessage("help-0")."\n".
                        LanguageManager::translateMessage("help-1")."\n".
                        LanguageManager::translateMessage("help-2")."\n".
                        LanguageManager::translateMessage("help-3")."\n".
                        LanguageManager::translateMessage("help-4")."\n");
                        break;
                    case "create":
                    case "add":
                    case "generate":
                        if(!$sender->hasPermission("mw.cmd.create")) {
                            $sender->sendMessage("§cYou have not permissions to use this command!");
                            break;
                        }
                        if(empty($args[1])) {
                            $sender->sendMessage(self::getPrefix().LanguageManager::translateMessage("create-usage"));
                            break;
                        }
                        $this->bgenerator->generateLevel($args[1], $args[2], $args[3]);
                        $sender->sendMessage(self::getPrefix().str_replace("%1", $args[1], LanguageManager::translateMessage("create.generating")));
                        break;
                    case "teleport":
                    case "tp":
                    case "move":
                        if(!$sender->hasPermission("mw.cmd.teleport")) {
                            $sender->sendMessage("§cYou have not permissions to use this command!");
                            break;
                        }
                        if(empty($args[1])) {
                            $sender->sendMessage(self::getPrefix()."§cUsage: §7/mw teleport <level> [player]");
                            break;
                        }
                        if(!Server::getInstance()->isLevelGenerated($args[1])) {
                            $sender->sendMessage(self::getPrefix()."§cLevel is not generated yet! Try /mw create for generate level.");
                            break;
                        }
                        if(!Server::getInstance()->isLevelLoaded($args[1])) {
                            Server::getInstance()->loadLevel($args[1]);
                            $this->getLogger()->debug("Loading level {$args[1]}...");
                        }
                        if(isset($args[2])) {
                            $player = $this->getServer()->getPlayer($args[2]);
                            if($player->isOnline()) {
                                $player->teleport(Server::getInstance()->getLevelByName($args[1])->getSafeSpawn(), 0, 0);
                                $player->sendMessage(self::getPrefix()."§aYou are teleported to level {$args[1]}.");
                                $sender->sendMessage(self::getPrefix()."§aPlayer {$player->getName()} is teleported to level {$args[1]}!");
                                break;
                            }
                            else {
                                $sender->sendMessage(self::getPrefix()."§cPlayer does not exists!");
                                break;
                            }
                        }
                        else {
                            $sender->teleport(Server::getInstance()->getLevelByName($args[1])->getSafeSpawn(), 0, 0);
                            $sender->sendMessage(self::getPrefix()."§aYou are teleported to {$args[1]}!");
                        }
                        break;
                    case "import":
                        if(!$sender->hasPermission("mw.cmd.import")) {
                            $sender->sendMessage("§cYou have not permissions to use this command!");
                            break;
                        }
                        if(empty($args[1])) {
                            $sender->sendMessage(self::getPrefix()."§cUsage: §7/mw import <FolderName>");
                            break;
                        }
                        $zipPath = ConfigManager::getDataPath()."levels/{$args[1]}.zip";
                        if(!file_exists($zipPath)) {
                            $sender->sendMessage(self::getPrefix()."§cZip does not exists!");
                            break;
                        }
                        $zip = new \ZipArchive;
                        $zip->open($zipPath);
                        $zip->extractTo(ConfigManager::getDataPath()."worlds/");
                        $zip->close();
                        unset($zip);
                        $this->getServer()->loadLevel($args[1]);
                        $sender->sendMessage(self::getPrefix()."§aLevel imported!");
                        break;
                    case "list":
                    case "ls":
                    case "levels":
                    case "worlds":
                        if(!$sender->hasPermission("mw.cmd.list")) {
                            $sender->sendMessage("§cYou have not permissions to use this command!");
                            break;
                        }
                        $list = scandir(ConfigManager::getDataPath()."worlds/");
                        $sender->sendMessage(self::getPrefix()."§aLevels:".implode(", ", array_shift(array_shift($list))));
                        break;
                }
            }
            else {
                if(!$sender->hasPermission("mw.cmd.help")) {
                    $sender->sendMessage("§cYou have not permissions to use this command!");
                }
                else {
                    $sender->sendMessage(self::getPrefix()."§cUsage: §7/mw help");
                }
            }
        }

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
