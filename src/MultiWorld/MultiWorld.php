<?php

namespace MultiWorld;

use MultiWorld\Event\EventListener;
use MultiWorld\Generator\AdvancedGenerator;
use MultiWorld\Generator\BasicGenerator;
use MultiWorld\Task\DelayedTask\RegisterGeneratorTask;
use MultiWorld\Util\ConfigManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class MultiWorld extends PluginBase {

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
        $this->configmgr->initConfig();

        // generators
        $this->bgenerator = new BasicGenerator($this);
        $this->agenerator = new AdvancedGenerator($this);

        // tasks
        #$this->registerGeneratorTask = new RegisterGeneratorTask($this);
        #$this->getServer()->getScheduler()->scheduleDelayedTask($this->registerGeneratorTask, 5*60);
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
                        $sender->sendMessage("§b--- == §c[§aMultiWorld§c] §b== ---\n".
                        "§e/mw create §7: §9Generate level\n".
                        "§e/mw teleport §7: §9Teleport to level\n");
                        break;
                    case "create":
                    case "add":
                    case "generate":
                        if(!$sender->hasPermission("mw.cmd.create")) {
                            $sender->sendMessage("§cYou have not permissions to use this command!");
                            break;
                        }
                        if(empty($args[1])) {
                            $sender->sendMessage(self::getPrefix()."§cUsage: §7/mw create <name> [seed] [generator]");
                            break;
                        }
                        $this->bgenerator->generateLevel($args[1], $args[2], $args[3]);
                        $sender->sendMessage(self::getPrefix()."§aGenerating level {$args[0]}");
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
                            $this->getLogger()->debug("[MultiWorld] Loading level {$args[1]}...");
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
