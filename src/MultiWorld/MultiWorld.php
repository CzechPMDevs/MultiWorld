<?php

namespace MultiWorld;

use MultiWorld\Events\EventListener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class MultiWorld extends PluginBase {

    /** @var  EventListener */
    public $listener;

    /** @var  WorldManager */
    public $manager;

    public static $prefix;

    public function onEnable() {
        $this->loadConfig();
        $this->registerListener();
        $this->registerManager();
        $this->getServer()->getPluginManager()->registerEvents($this->listener, $this);
    }

    public function registerListener() {
        $this->listener = new EventListener($this);
    }

    public function registerManager() {

    }

    public static function getPermissionMessage() {
        return "§cYou do not have permission to use this command";
    }

    public function loadConfig() {
        if(!file_exists($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if(!is_file($this->getDataFolder()."/config.yml")) {
            $this->saveResource("/config.yml");
            self::$prefix = $this->getConfig()->get("prefix");
        }
    }

    /**
     * @param int $page
     * @return string
     */
    public function getHelp($page) {
        $title = "§3---== §aWorldManager §3==---";
        switch ($page) {
            case 1:
                $p1 = $title." §7(1/3)\n".
                    "§9/mw create §6Create new World\n" .
                    "§9/mw setspawn §6Sets world spawn\n" .
                    "§9/mw setdefault §6Set world as the default level\n" .
                    "§9/mw sethub §6Sets server hub\n";
                return $p1;
            case 2:
                $p2 = $title." §7(2/3)\n".
                    "§9/mw list §6Displays all worlds\n" .
                    "§9/mw tp §6Moves you to the world\n" .
                    "§9/mw genlist §6Displays all generators\n" .
                    "§9/mw load §6Retrieves world\n";
                return $p2;
            case 3:
                $p3 = $title." §7(3/3)\n".
                    "§9/mw unload §6Unload wold\n" .
                    "§9/mw delete §6Delete world\n" .
                    "§9/mw info §6Info about level\n" .
                    "§9/mw rename §6Rename world\n";
                return $p3;
            /*case 4:
                $p4 = $title." §7(4/4)\n".
                    "§9/mw setdimension §6Set world dimension\n".
                    "§9/mw setgm §6Set world gamemode";
                return $p4;*/
        }
    }

    public function onCommand(CommandSender $s, Command $cmd, $label, array $args)
    {
        if ($cmd->getName() == "multiworld" && $s instanceof Player) {
            if (isset($args[0])) {
                switch ($args[0]) {
                    case "help":
                        if (!$s->hasPermission("mw.cmd.help")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (empty($args[1])) {
                            $s->sendMessage($this->getHelp(1));
                        } else {
                            switch ($args[1]) {
                                case "1":
                                    $s->sendMessage($this->getHelp(1));
                                    break;
                                case "2":
                                    $s->sendMessage($this->getHelp(2));
                                    break;
                                case "3":
                                    $s->sendMessage($this->getHelp(3));
                                    break;
                                /*case "4":
                                    $s->sendMessage($this->getHelp(4));
                                    break;*/
                                default:
                                    $s->sendMessage($this->getHelp(1));
                                    break;
                            }
                        }
                        break;
                    case "create":
                    case "new":
                    case "add":
                        if (!$s->hasPermission("mw.cmd.create")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (count($args) >= 4) {
                            if (!is_numeric($args[3])) {
                                $s->sendMessage(self::$prefix . "§cSeed must be formatted numbers!");
                            } else {
                                $this->manager->generate($s, $args[1], $args[2], $args[3]);
                            }
                        } else {
                            $s->sendMessage(self::$prefix . "§7Usage: §c/mw <create | new | add> <name> <generator> <seed>");
                        }

                        break;
                    case "setspawn":
                    case "setworldspawn":
                        if (!$s->hasPermission("mw.cmd.setspawn")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (empty($args[1])) {
                            $s->getLevel()->setSpawnLocation($s);
                            $s->sendMessage(self::$prefix . "§aSpawn location updated!");
                        }
                        break;
                    case "setdefault":
                        if (!$s->hasPermission("mw.cmd.setdefault")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (empty($args[1])) {
                            $this->getServer()->setDefaultLevel($s->getLevel());
                            $s->sendMessage(self::$prefix . "§aDefault level updated!");
                        } else {
                            $this->getServer()->setDefaultLevel($this->getServer()->getLevelByName($args[1]));
                            $s->sendMessage(self::$prefix . "§aDefault level updated!");
                        }
                        break;
                    case "sethub":
                    case "setlobby":
                        if (!$s->hasPermission("mw.cmd.sethub")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (empty($args[1])) {
                            $this->getServer()->setDefaultLevel($s->getLevel());
                            $s->getLevel()->setSpawnLocation($s);
                            $s->sendMessage(self::$prefix . "§aLobby position updated!");
                        }

                        break;
                    case "list":
                    case "ls":
                        if (!$s->hasPermission("mw.cmd.list")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (isset($args[1])) {
                            switch ($args[1]) {
                                case "loded":
                                    $s->sendMessage(self::$prefix . "Loaded levels: {$this->manager->getWorldList(1)}");
                                    break;
                                case "all":
                                    $s->sendMessage(self::$prefix . "All levels: {$this->manager->getWorldList(2)}");
                                    break;
                                default:
                                    $s->sendMessage(self::$prefix . "§7Usage: §c/wm ls <loaded | all>");
                                    break;
                            }
                        } else {
                            $s->sendMessage(self::$prefix . "§7Usage: §c/wm ls <loaded | all>");
                        }
                        break;
                    case "teleport":
                    case "tp":
                        if (!$s->hasPermission("mw.cmd.tp")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (isset($args[1]) && empty($args[2])) {
                            $this->manager->teleportToWorld($s,$args[1]);
                        }
                        elseif (isset($args[1]) && isset($args[2]))  {
                            if($this->getServer()->getPlayer($args[2])->isOnline()) {
                                $this->manager->teleportToWorld($this->getServer()->getPlayer($args[2]),$args[1]);
                                $s->sendMessage(self::$prefix."§aTeleporting player {$args[2]} to {$args[1]}...");
                            }
                            else {
                                $s->sendMessage(self::$prefix."§cPlayer {$args[2]} is not online.");
                            }
                        }
                        break;
                    case "generators":
                    case "genlist":
                    case "generatorlist":
                        if (!$s->hasPermission("mw.cmd.genlist")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        $s->sendMessage(self::$prefix."§2Generators: §aDefault, Nether, Flat, Void");
                        break;
                    case "load":
                        if (!$s->hasPermission("mw.cmd.load")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (isset($args[1])) {
                            $this->manager->unload($args[1]);
                        } else {
                            $s->sendMessage(self::$prefix."§7Usage: §c/mw load <level>");
                        }
                        break;
                    case "unload":
                        if (!$s->hasPermission("mw.cmd.unload")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (isset($args[1])) {
                            $this->manager->unload($args[1]);
                        } else {
                            $s->sendMessage(self::$prefix."§7Usage: §c/mw unload <level>");
                        }
                        break;
                    case "delete":
                    case "remove":
                    case "del":
                    case "rm":
                        if (!$s->hasPermission("mw.cmd.delete")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (isset($args[1])) {
                            $this->manager->delete($this->getServer()->getLevelByName($args[1]), $s);
                        } else {
                            $s->sendMessage(self::$prefix."§7Usage:§c /mw delete <level>");
                        }
                        break;
                    case "info":
                        if (!$s->hasPermission("mw.cmd.info")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if(isset($args[0])) {
                            $this->manager->sendLevelInfo($s,$args[1]);
                        }
                        else {
                            $this->manager->sendLevelInfo($s,$s->getLevel()->getName());
                        }
                        break;
                    case "rename":
                        if (!$s->hasPermission("mw.cmd.rename")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (isset($args[1]) && isset($args[2])) {
                            $this->manager->rename($args[1],$args[2],$s);
                        } else {
                            $s->sendMessage(self::$prefix . "§7Usage: §c/mw rename <oldname> <newname>");
                        }
                        break;
                    default:
                        if (!$s->hasPermission("wm.cmd.help")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        break;
                }
            } else {
                $s->sendMessage(self::$prefix . "§7Usage: §c/mw help");
            }
        }
    }
}
