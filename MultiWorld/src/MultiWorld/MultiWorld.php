<?php

namespace MultiWorld;

use MultiWorld\Generator\MultiWorldGenerator;
use MultiWorld\Util\ConfigManager;
use MultiWorld\Util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

/**
 * Class MultiWorld
 * @package MultiWorld
 */
class MultiWorld extends PluginBase {

    const NAME = "MultiWorld";
    const VERSION = "1.3.0";
    const AUTHOR = "GamakCZ";
    const GITHUB = "https://github.com/CzechPMDevs/MultiWorld/";

    /** @var  MultiWorld */
    static $instance;

    /** @var  string $prefix */
    static $prefix;

    /** @var  ConfigManager */
    public $configmgr;

    /** @var  LanguageManager */
    public $langmgr;

    /** @var  MultiWorldGenerator $multiWorldGenerator */
    public $multiWorldGenerator;

    public function onEnable() {

        // INSTANCE
        self::$instance = $this;

        // utils
        $this->configmgr = new ConfigManager($this);
        $this->langmgr = new LanguageManager($this);
        $this->multiWorldGenerator = new MultiWorldGenerator($this);
        $this->configmgr->initConfig();
        $this->langmgr->loadLang();
        $this->multiWorldGenerator->loadGenerators();

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
     * @param CommandSender $sender
     * @param string $command
     * @return bool
     */
    function checkPerms(CommandSender $sender, string $command):bool {
        if($sender instanceof Player) {
            if(!$sender->hasPermission("mw.cmd.{$command}")) {
                $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return true;
        }
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
        if (in_array($command, ["mw", "wm", "multiworld"])) {
            if (empty($args[0])) {
                $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("default-usage"));
                return false;
            }

            switch (strtolower($args[0])) {
                case "help":
                case "?":
                    if (!$this->checkPerms($sender, "help")) return false;
                    $sender->sendMessage(LanguageManager::translateMessage("help-0") . "\n" .
                        LanguageManager::translateMessage("help-1") . "\n" .
                        LanguageManager::translateMessage("help-2") . "\n" .
                        LanguageManager::translateMessage("help-3") . "\n" .
                        LanguageManager::translateMessage("help-4") . "\n" .
                        LanguageManager::translateMessage("help-5") . "\n");
                    return false;
                case "create":
                case "new":
                case "add":
                case "generate":
                    if (!$this->checkPerms($sender, "create")) return false;
                    if (empty($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("create-usage"));
                        return false;
                    }
                    if ($this->getServer()->isLevelGenerated($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("create-exists")));
                        return false;
                    }
                    $seed = null;
                    $generator = null;
                    count($args) < 3 ? $seed = rand(rand(1, 10), rand(50, 99999999999999)) : $seed = $args[2];
                    count($args) < 4 ? $generator = "normal" : $generator = $args[3];
                    strtolower($generator) == "nether" ? $generator = "hell" : $generator = strtolower($generator);
                    if (Generator::getGeneratorName(Generator::getGenerator($generator)) != strtolower($generator)) {
                        $sender->sendMessage(str_replace("%1", strtolower($generator), LanguageManager::translateMessage("create-gennotexists")));
                        return false;
                    }
                    is_numeric($seed) ? $seed = (int)$seed : $seed = intval($seed);
                    $this->getServer()->generateLevel($args[1], $seed, Generator::getGenerator($generator));
                    $sender->sendMessage(MultiWorld::getPrefix() . str_replace("%1", $args[1], str_replace("%2", $seed, str_replace("%3", strtolower($generator), LanguageManager::translateMessage("create-done")))));
                    return false;
                case "teleport":
                case "tp":
                case "move":
                    if (!$this->checkPerms($sender, "teleport")) return false;
                    if (empty($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("teleport-usage"));
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

                    if ($sender instanceof Player) {
                        if (empty($args[2])) {
                            $sender->teleport($this->getServer()->getLevelByName($args[1])->getSpawnLocation());
                            $sender->sendMessage(MultiWorld::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("teleport-done-1")));
                            return false;
                        }
                    }
                    if (isset($args[2])) {
                        $player = $this->getServer()->getPlayer($args[2]);
                        if (!is_null($player) && $player->isOnline()) {
                            $player->teleport($this->getServer()->getLevelByName($args[1])->getSpawnLocation());
                            $player->sendMessage(MultiWorld::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("teleport-done-1")));
                            $sender->sendMessage(MultiWorld::getPrefix() . str_replace("%1", $args[1], str_replace("%2", $args[2], LanguageManager::translateMessage("teleport-done-2"))));
                            return false;
                        } else {
                            $sender->sendMessage(MultiWorld::getPrefix() . str_replace("%1", $args[2], LanguageManager::translateMessage("teleport-playernotexists")));
                            return false;
                        }
                    }
                    return false;
                case "ls":
                case "list":
                    if (!$this->checkPerms($sender, "list")) return false;
                    $allLevels = scandir(ConfigManager::getDataPath() . "worlds");
                    unset($allLevels[0]);
                    unset($allLevels[1]);
                    $loaded = [];
                    foreach ($this->getServer()->getLevels() as $level) {
                        array_push($loaded, $level->getName());
                    }
                    $list = implode(", ", $allLevels);
                    $sender->sendMessage(MultiWorld::getPrefix() . str_replace("%1", $list, LanguageManager::translateMessage("list-done")));
                    return false;
                case "load":
                case "ld":
                    if (!$this->checkPerms($sender, "load")) return false;
                    if (empty($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("load-usage"));
                        return false;
                    }
                    if (!$this->getServer()->isLevelGenerated($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("load-levelnotexists"));
                        return false;
                    }
                    if ($this->getServer()->isLevelLoaded($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("load-loaded"));
                        return false;
                    }
                    $this->getServer()->loadLevel($args[1]);
                    $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("load-done"));
                    return false;
                case "unload":
                case "uld":
                    if (!$this->checkPerms($sender, "unload")) return false;
                    if (empty($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("unload-usage"));
                        return false;
                    }
                    if (!$this->getServer()->isLevelGenerated($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("unload-levelnotexists"));
                        return false;
                    }
                    if (!$this->getServer()->isLevelLoaded($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("unload-unloaded"));
                        return false;
                    }
                    $this->getServer()->unloadLevel($this->getServer()->getLevelByName($args[1]));
                    $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("unload-done"));
                    return false;
                case "delete":
                case "remove":
                case "del":
                case "rm":
                    if (!$this->checkPerms($sender, "delete")) return false;
                    if (empty($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("delete-usage"));
                        return false;
                    }
                    if (!$this->getServer()->isLevelGenerated($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("delete-levelnotexists"));
                        return false;
                    }
                    $levelName = $args[1];
                    $folderName = $args[1];
                    if ($this->getServer()->isLevelLoaded($levelName)) {
                        $level = $this->getServer()->getLevelByName($levelName);
                        if (count($level->getPlayers()) != 0) {
                            foreach ($level->getPlayers() as $player) {
                                $player->teleport($this->getServer()->getDefaultLevel()->getSpawnLocation());
                            }
                        }
                        $folderName = $level->getFolderName();
                        $level->unload();
                    }
                    $folderPath = ConfigManager::getDataPath() . "worlds/{$folderName}";
                    try {
                        if(is_dir($folderPath)) {
                            if(is_dir($folderPath."/region")) {
                                foreach (glob($folderPath."/region/*.mca") as $chunks) {
                                    unlink($chunks);
                                }
                            }
                            else {
                                $this->getLogger()->critical("DELETE ERROR #1");
                            }
                            rmdir($folderPath."/region");
                            unlink($folderPath."/level.dat");
                            foreach (scandir($folderPath) as $file) {
                                if(!in_array($file, [".", ".."])) {
                                    is_dir($file) ? rmdir($folderPath.$file) : unlink($folderPath.$file);
                                }
                            }
                            rmdir($folderPath);
                        }
                        else {
                            $this->getLogger()->critical("DELETE ERROR #2");
                        }
                    }
                    catch (\Exception $exception) {
                        $github = MultiWorld::GITHUB;
                        $sender->sendMessage("§cError when deleting world. Submit issue to {$github}\n§7Error: {$exception->getMessage()}");
                        $this->getLogger()->critical("\n§cError when deleting world. Submit issue to {$github}\n§7Error: {$exception->getMessage()}");
                    }

                    $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("delete-done"));
                    return false;
                case "update":
                case "ue":
                case "upte":
                    // commmand: /mw [0]ue [1]<spawn|lobby|default> [options:[2]x [3]y [4]z [5](level)]
                    if (!$this->checkPerms($sender, "update")) return false;
                    if(empty($args[1])) {
                        $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("update-usage"));
                        return false;
                    }
                    switch (strtolower($args[1])) {
                        case "spawn":
                        case "0":
                            if($sender instanceof Player) {
                                $sender->getLevel()->setSpawnLocation($sender->asVector3());
                                $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $sender->getLevel()->getName(), LanguageManager::translateMessage("update-spawn-done")));
                            }
                            else {
                                if(!(count($args) < 7)) {
                                    $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("update-spawn-usage"));
                                    return false;
                                }
                                if(!$this->getServer()->isLevelGenerated($args[5])) {
                                    $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("update-levelnotexists"));
                                    return false;
                                }
                                if(!$this->getServer()->isLevelLoaded($args[5])) {
                                    $this->getServer()->loadLevel($args[5]);
                                }
                                $level = $this->getServer()->getLevelByName($args[5]);
                                $level->setSpawnLocation(new Vector3(intval($args[2]), intval($args[3]), intval($args[4])));
                                $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $level->getName(), LanguageManager::translateMessage("update-spawn-done")));
                            }
                            return false;
                        case "lobby":
                        case "1":
                            if($sender instanceof Player) {
                                $level = $sender->getLevel();
                                $this->getServer()->setDefaultLevel($level);
                                $level->setSpawnLocation($sender->asVector3());
                                $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $level->getName(), LanguageManager::translateMessage("update-lobby-done")));
                            }
                            else {

                            }
                            return false;
                    }
                    return false;
                default:
                    if ($this->checkPerms($sender, "help")) {
                        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("default-usage"));
                    }
                    return false;
            }
        }
    }
}

