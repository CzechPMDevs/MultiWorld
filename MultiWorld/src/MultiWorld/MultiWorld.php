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
use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\generator\Generator;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class MultiWorld extends PluginBase {

    const NAME = "MultiWorld";
    const VERSION = "1.3.0 [BETA 3] [PocketMine]";
    const AUTHOR = "GamakCZ";
    const GITHUB = "https://github.com/CzechPMDevs/MultiWorld/";

    /** @var  MultiWorld */
    static $instance;

    /** @var  string $prefix */
    static $prefix;

    ##\
    ### > Events
    ##/

    /** @var  EventListener */
    public $eventListener;

    ##\
    ### > Utils
    ##/

    /** @var  ConfigManager */
    public $configmgr;

    /** @var  LanguageManager */
    public $langmgr;

    public function onEnable() {
        // INSTANCE
        self::$instance = $this;

        // events
        $this->getServer()->getPluginManager()->registerEvents($this->eventListener = new EventListener($this), $this);

        // utils
        $this->configmgr = new ConfigManager($this);
        $this->langmgr = new LanguageManager($this);
        $this->configmgr->initConfig();
        $this->langmgr->loadLang();

        if($this->getServer()->getName() != "PocketMine-MP") {
            $this->getLogger()->critical("§cMultiWorld does not support {$this->getServer()->getName()}");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }

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
                $this->getLogger()->info("\n§5**********************************************\n".
                    "§6 ---- == §c[§aMultiWorld§c]§6== ----\n".
                    "§9> Version: §e{$this->getDescription()->getVersion()}\n".
                    "§9> Author: §eCzechPMDevs :: GamakCZ, Kyd\n".
                    "§9> GitHub: §e".self::GITHUB."\n".
                    "§9> Package: §ePhar\n".
                    "§9> Language: §e".LanguageManager::getLang()."\n".
                    "§5*********************************************");
            }
            else {
                $this->getLogger()->info("\n§5**********************************************\n".
                    "§6 ---- == §c[§aMultiWorld§c]§6== ----\n".
                    "§9> Version: §e{$this->getDescription()->getVersion()}\n".
                    "§9> Author: §eCzechPMDevs :: GamakCZ, Kyd\n".
                    "§9> GitHub: §egithub.com/CzechMPDevs/MultiWorld\n".
                    "§9> Package: §esrc\n".
                    "§9> Language: §e".LanguageManager::getLang()."\n".
                    "§5*********************************************");
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
    public static function getInstance() {
        return self::$instance;
    }

    /**
     * @return string
     */
    public static function getPrefix() {
        return ConfigManager::getPrefix();
    }

    /**
     * @param CommandSender $sender
     * @param Command $cmd
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool {
        $command = $cmd->getName();
        if(in_array($command, ["mw", "wm", "multiworld"])) {
            if(empty($args[0])) {
                $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("default-usage"));
                return false;
            }

            switch (strtolower($args[0])) {
                case "create":
                case "new":
                case "add":
                case "generate":
                    if(($sender instanceof Player && !$sender->hasPermission("mw.cmd.create")) || !$sender instanceof ConsoleCommandSender) {
                        $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                        return false;
                    }
                    if(empty($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("create-usage"));
                        return false;
                    }
                    if($this->getServer()->isLevelGenerated($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[1], LanguageManager::translateMessage("create-exists")));
                        return false;
                    }
                    $seed = null;
                    $generator = null;
                    count($args) < 3 ? $seed = rand(1,99999) : $seed = $args[2];
                    count($args) < 4 ? $generator = "normal" : $generator = $args[3];
                    strtolower($generator) == "nether" ? $generator = "hell" : $generator = strtolower($generator);
                    if(Generator::getGeneratorName(Generator::getGenerator($generator)) != strtolower($generator)) {
                        $sender->sendMessage(str_replace("%1", strtolower($generator), LanguageManager::translateMessage("create-gennotexists")));
                        return false;
                    }
                    is_numeric($seed) ? $seed = (int)$seed : $seed = intval($seed);
                    $this->getServer()->generateLevel($args[1], $seed, Generator::getGenerator($generator));
                    $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[1], str_replace("%2", $seed, str_replace("%3", strtolower($generator), LanguageManager::translateMessage("create-done")))));
                    return false;
                case "teleport":
                case "tp":
                case "move":
                    if (empty($args[1])) {
                        if(($sender instanceof Player && $sender->hasPermission("mw.cmd.teleport")) || ($sender instanceof ConsoleCommandSender)) {
                            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("teleport-usage"));
                        }
                        return false;
                    }

                    if (!Server::getInstance()->isLevelGenerated($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("teleport-levelnotexists"));
                        return false;
                    }

                    if (!Server::getInstance()->isLevelLoaded($args[1])) {
                        Server::getInstance()->loadLevel($args[1]);
                        $this->getLogger()->debug(MultiWorld::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("teleport-load")));
                    }

                    if($sender instanceof Player) {
                        if(empty($args[2])) {
                            $sender->teleport($this->getServer()->getLevelByName($args[1])->getSpawnLocation());
                            $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[1], LanguageManager::translateMessage("teleport-done-1")));
                            return false;
                        }
                    }
                    if(isset($args[2])) {
                        $player = $this->getServer()->getPlayer($args[2]);
                        if($player != null && $player->isOnline()) {
                            $player->teleport($this->getServer()->getLevelByName($args[1])->getSpawnLocation());
                            $player->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[1], LanguageManager::translateMessage("teleport-done-1")));
                            $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[1], str_replace("%2", $args[2], LanguageManager::translateMessage("teleport-done-2"))));
                            return false;
                        }
                        else {
                            $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[2], LanguageManager::translateMessage("teleport-playernotexists")));
                            return false;
                        }
                    }
                    return false;
                default:
                    if(($sender->hasPermission("mw.cmd.help")) || $sender instanceof ConsoleCommandSender) {
                        $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("default-usage"));
                    }
                    else {
                        $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                    }
                    return false;
            }
        }
    }
}

