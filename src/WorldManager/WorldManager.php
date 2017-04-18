<?php

namespace WorldManager;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\lang\BaseLang;
use pocketmine\level\generator\Generator;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\Config;

class WorldManager extends PluginBase implements Listener {

    public $prefix;
    public $server;
    public $lang;

    /** @var Config */
    public $config;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->server = $this->getServer()->getName();
        $this->getLogger()->info("§aEnabling WorldManager...");
        $this->loadConfig();
    }

    public static function getPermissionMessage() {
        return "§cYou do not have permission to use this command";
    }

    public function loadConfig() {
        // update
        if(!is_file($this->getDataFolder()."/config.yml")) {
            @mkdir($this->getDataFolder());
            $cfg = new Config($this->getDataFolder()."/config.yml", Config::YAML);
            $cfg->set("prefix", "§2[WorldManager]");
            $cfg->set("creative", "");
            #$cfg->set("language", "eng");
            $cfg->save();
        }
        else {
            $cfg = new Config($this->getDataFolder()."/config.yml", Config::YAML);
            if($cfg->get("creative") == null) {
                $cfg->set("creative", "");
                $cfg->set("prefix", "[§2WorldManager]");
                $cfg->save();
            }
        }
        $cfg = new Config($this->getDataFolder()."/config.yml", Config::YAML);
        $this->prefix = "{$cfg->get("prefix")} ";
        $this->config = $cfg;
    }

    public function createWorld(Player $p, $name, $generator, $seed) {
        switch($generator) {
            case "default":
                if($this->getServer()->getLevelByName($name) == null) {
                    $this->getServer()->generateLevel($name,$seed,Generator::getGenerator("default"));
                    foreach ($this->getServer()->getOnlinePlayers() as $pl) {
                        if($pl->hasPermission("wm.cmd.create")) {
                            $pl->sendMessage($this->prefix."§6Making wolrd with name {$name}, seed {$seed} and generator default.");
                        }
                    }
                    $p->sendMessage($this->prefix."§aThe world was created in the background.");
                    $this->getServer()->loadLevel($name);
                }
                else {
                    $p->sendMessage($this->prefix."§cWorld exists.");
                }
                break;
            case "flat":
                if($this->getServer()->getLevelByName($name) == null) {
                    $this->getServer()->generateLevel($name,$seed,Generator::getGenerator("flat"));
                    foreach ($this->getServer()->getOnlinePlayers() as $pl) {
                        if($pl->hasPermission("wm.cmd.create")) {
                            $pl->sendMessage($this->prefix."§6Making wolrd with name {$name}, seed {$seed} and generator flat.");
                        }
                    }
                    $p->sendMessage($this->prefix."§aThe world was created in the background.");
                    $this->getServer()->loadLevel($name);
                }
                else {
                    $p->sendMessage($this->prefix."§cWorld exists.");
                }
                break;
            case "nether":
                if($this->getServer()->getLevelByName($name) == null) {
                    $this->getServer()->generateLevel($name,$seed,Generator::getGenerator("hell"));
                    foreach ($this->getServer()->getOnlinePlayers() as $pl) {
                        if($pl->hasPermission("wm.cmd.create")) {
                            $pl->sendMessage($this->prefix."§6Making wolrd with name {$name}, seed {$seed} and generator nether.");
                        }
                    }
                    $p->sendMessage($this->prefix."§aThe world was created in the background.");
                    $this->getServer()->loadLevel($name);
                }
                else {
                    $p->sendMessage($this->prefix."§cWorld exists.");
                }
                break;
            case "void":
                if($this->getServer()->getLevelByName($name) == null) {
                    $this->getServer()->generateLevel($name,$seed,Generator::getGenerator("void"));
                    foreach ($this->getServer()->getOnlinePlayers() as $pl) {
                        if($pl->hasPermission("wm.cmd.create")) {
                            $pl->sendMessage($this->prefix."§6Making wolrd with name {$name}, seed {$seed} and generator void.");
                        }
                    }
                    $p->sendMessage($this->prefix."§aThe world was created in the background.");
                    $this->getServer()->loadLevel($name);
                }
                else {
                    $p->sendMessage($this->prefix."§cWorld exists.");
                }
                break;
            default:
                $p->sendMessage($this->prefix."§cThe generator does not exist!");
                break;
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
                $p1 = $title." §7(1/4)\n".
                    "§9/wm create §6Create new World\n" .
                    "§9/wm setspawn §6Sets world spawn\n" .
                    "§9/wm setdefault §6Set world as the default level\n" .
                    "§9/wm sethub §6Sets server hub\n";
                return $p1;
            case 2:
                $p2 = $title." §7(2/4)\n".
                    "§9/wm list §6Displays all worlds\n" .
                    "§9/wm tp §6Moves you to the world\n" .
                    "§9/wm genlist §6Displays all generators\n" .
                    "§9/wm load §6Retrieves world\n";
                return $p2;
            case 3:
                $p3 = $title." §7(3/4)\n".
                    "§9/wm unload §6Unload wold\n" .
                    "§9/wm delete §6Delete world\n" .
                    "§9/wm info §6Info about level\n" .
                    "§9/wm rename §6Rename world\n";
                return $p3;
            case 4:
                $p4 = $title." §7(4/4)\n".
                    "§9/wm setdimension §6Set world dimension\n".
                    "§9/wm setgm §6Set world gamemode";
                return $p4;
        }
    }

    public function onCommand(CommandSender $s, Command $cmd, $label, array $args)
    {
        if ($cmd->getName() == "worldmanager" && $s instanceof Player) {
            if (isset($args[0])) {
                switch ($args[0]) {
                    case "help":
                        if (!$s->hasPermission("wm.cmd.help")) {
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
                                case "4":
                                    $s->sendMessage($this->getHelp(4));
                                    break;
                                default:
                                    $s->sendMessage($this->getHelp(1));
                                    break;
                            }
                        }
                        break;
                    case "create":
                    case "new":
                    case "add":
                        if (!$s->hasPermission("wm.cmd.create")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (count($args) >= 4) {
                            if (!is_numeric($args[3])) {
                                $s->sendMessage($this->prefix . "§cSeed must be formatted numbers!");
                            } else {
                                $this->createWorld($s, $args[1], strtolower($args[2]), $args[3]);
                            }
                        } else {
                            $s->sendMessage($this->prefix . "§7Usage: §c/wm <create | new | add> <name> <generator> <seed>");
                        }

                        break;
                    case "setspawn":
                    case "setworldspawn":
                        if (!$s->hasPermission("wm.cmd.setspawn")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (empty($args[1])) {
                            $s->getLevel()->setSpawnLocation($s);
                            $s->sendMessage($this->prefix . "§aSpawn location updated!");
                        }
                        break;
                    case "setdefault":
                        if (!$s->hasPermission("wm.cmd.setdefault")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (empty($args[1])) {
                            $this->getServer()->setDefaultLevel($s->getLevel());
                            $s->sendMessage($this->prefix . "§aDefault level updated!");
                        } else {
                            $this->getServer()->setDefaultLevel($this->getServer()->getLevelByName($args[1]));
                            $s->sendMessage($this->prefix . "§aDefault level updated!");
                        }
                        break;
                    case "sethub":
                    case "setlobby":
                        if (!$s->hasPermission("wm.cmd.sethub")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (empty($args[1])) {
                            $this->getServer()->setDefaultLevel($s->getLevel());
                            $s->getLevel()->setSpawnLocation($s);
                            $s->sendMessage($this->prefix . "§aLobby position updated!");
                        }

                        break;
                    case "list":
                    case "ls":
                        if (!$s->hasPermission("wm.cmd.list")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (isset($args[1])) {
                            switch ($args[1]) {
                                case "loded":
                                    $s->sendMessage($this->prefix . "Loaded levels:");
                                    $data = implode($this->getServer()->getLevels());
                                    $s->sendMessage($this->prefix."§7Loaded levels: §a".$data);
                                    break;
                                case "all":
                                    $s->sendMessage($this->prefix . "All levels:");
                                    $data = scandir($this->getServer()->getDataPath()."worlds");
                                    $data = implode(", ",$data);
                                    $data = str_replace(", .", "", $data);
                                    $data = str_replace(".", "", $data);
                                    $s->sendMessage($this->prefix."§7All levels: §a".$data);
                                    break;
                                default:
                                    $s->sendMessage($this->prefix . "§7Usage: §c/wm ls <loaded | all>");
                                    break;
                            }
                        }
                        else {
                            $s->sendMessage($this->prefix."§7Usage: §c/wm ls <loaded | all>");
                        }
                        break;
                    case "teleport":
                    case "tp":
                        if (!$s->hasPermission("wm.cmd.tp")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (isset($args[1]) && empty($args[2])) {
                            if (file_exists($this->getServer()->getDataPath() . "worlds/{$args[1]}")) {
                                $this->getServer()->loadLevel($args[1]);
                                $s->teleport($this->getServer()->getLevelByName($args[1])->getSafeSpawn());
                                $s->sendMessage($this->prefix . "§aYou've been teleported to the world {$args[1]}.");
                            } else {
                                $s->sendMessage($this->prefix . "§cWorld {$args[1]} does not exists.");
                            }
                        } elseif (isset($args[1]) && isset($args[2])) {
                            if (file_exists($this->getServer()->getDataPath() . "worlds/{$args[1]}")) {
                                if ($this->getServer()->getPlayer($args[2]) instanceof Player) {
                                    $p = $this->getServer()->getPlayer($args[2]);
                                    $p->teleport($this->getServer()->getLevelByName($args[1])->getSafeSpawn());
                                    $p->sendMessage($this->prefix . "§aYou've been teleported to the world {$args[1]}.");
                                } else {
                                    $s->sendMessage($this->prefix . "§cPlayer {$args[2]} is not online!");
                                }
                            } else {
                                $s->sendMessage($this->prefix . "§cWorld {$args[1]} does not exists.");
                            }

                        }
                        break;
                    case "generators":
                    case "genlist":
                    case "generatorlist":
                        if (!$s->hasPermission("wm.cmd.genlist")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        $s->sendMessage($this->prefix . "§2Generators: §aDefault, Nether, Flat, Void");
                        break;
                    case "load":
                        if (!$s->hasPermission("wm.cmd.load")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }

                        if (isset($args[1])) {
                            if (file_exists($this->getServer()->getDataPath() . "worlds/{$args[1]}")) {
                                $this->getServer()->loadLevel($args[1]);
                                $s->sendMessage($this->prefix . "§aThe level {$args[1]} was loaded in background");
                            } else {
                                $s->sendMessage($this->prefix . "§cWorld does not exists!");
                            }
                        } else {
                            $s->sendMessage($this->prefix . "§7Usage: §c/wm load <level>");
                        }
                        break;
                    case "unload":
                        if (!$s->hasPermission("wm.cmd.unload")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }

                        if (isset($args[1])) {
                            if ($this->getServer()->getLevelByName($args[1]) instanceof Level) {
                                $this->getServer()->unloadLevel($this->getServer()->getLevelByName($args[1]), true);
                                $s->sendMessage($this->prefix . "§aWorld {$args[1]} unloaded successfully");
                            }
                        } else {
                            $s->sendMessage($this->prefix . "§7Usage: §c/wm unload <level>");
                        }
                        break;
                    case "delete":
                    case "remove":
                    case "del":
                    case "rm":
                        if (!$s->hasPermission("wm.cmd.delete")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }

                        if (isset($args[1])) {
                            if (file_exists($this->getServer()->getDataPath() . "worlds/{$args[1]}")) {
                                foreach ($this->getServer()->getLevelByName($args[1])->getPlayers() as $pl) {
                                    $pl->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
                                }
                                $this->getServer()->unloadLevel($this->getServer()->getLevelByName($args[1]));
                                rmdir($this->getServer()->getDataPath() . "worlds/{$args[1]}");
                                $this->getServer()->shutdown(true, $this->prefix . "§aWorld {$args[1]} deleted successfully");
                            } else {
                                $s->sendMessage($this->prefix . "§cWorld {$args[1]} does not exists!");
                            }
                        } else {
                            $s->sendMessage($this->prefix . "§7Usage:§c /wm delete <level>");
                        }
                        break;
                    case "info":
                        if (!$s->hasPermission("wm.cmd.info")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }

                        if (isset($args[1]) && $this->getServer()->getLevelByName($args[1]) instanceof Level) {
                            $level = $this->getServer()->getLevelByName($args[1]);
                            $i1 = count($level->getPlayers());
                            $i2 = $level->getDimension();
                            if ($this->server != "Tesseract" || $this->server != "Genisys") $i2 = "§cerror";
                            $i3 = $level->getSeed();
                            $i4 = $level->getTime();
                            $s->sendMessage("§3---== §6{$level->getName()} §3==---\n" .
                                "§9Players: §e{$i1}\n" .
                                "§9Dimension: §e{$i2}\n" .
                                "§9Seed: §e{$i3}\n" .
                                "§9Time: §e{$i4}");
                        } else {
                            $level = $s->getLevel();
                            $i1 = count($level->getPlayers());
                            $i2 = $level->getDimension();
                            if ($this->server != "Tesseract" || $this->server != "Genisys") $i2 = "§cerror.";
                            $i3 = $level->getSeed();
                            $i4 = $level->getTime();
                            $s->sendMessage("§3---== §6{$level->getName()} §3==---\n" .
                                "§9Players: §e{$i1}\n" .
                                "§9Dimension: §e{$i2}\n" .
                                "§9Seed: §e{$i3}\n" .
                                "§9Time: §e{$i4}");
                        }
                        break;
                    case "rename":
                        if (!$s->hasPermission("wm.cmd.rename")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if (!empty($args[1]) && !empty($args[2])) {
                            if (file_exists($this->getServer()->getDataPath() . "worlds/{$args[1]}")) {
                                rename($this->getServer()->getDataPath() . "worlds/{$args[1]}", $this->getServer()->getDataPath() . "worlds/{$args[2]}");
                                $this->getServer()->shutdown(true, $this->prefix . "§aWorld {$args[1]} was renamed to {$args[2]}!");
                            } else {
                                $s->sendMessage($this->prefix . "§cWorld {$args[1]} does not exists");
                            }
                        } else {
                            $s->sendMessage($this->prefix . "§7Usage: §c/wm rename <oldname> <newname>");
                        }
                        break;
                    case "setdimension":
                    case "setdim":
                        if (!$s->hasPermission("wm.cmd.setdim")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        if ($this->server != "Tesseract" || $this->server != "Genisys") {
                            $s->sendMessage($this->prefix . "This command works only on Tesseract & Genisys");
                            break;
                        }
                        if (isset($args[1])) {
                            switch ($args[1]) {
                                case "nether":
                                    if (empty($args[2])) {
                                        $s->getLevel()->setDimension(Level::DIMENSION_NETHER);
                                        $s->sendMessage($this->prefix . "Level {$s->getLevel()->getName()} dimension changed to nether.");
                                    } else {
                                        $s->sendMessage($this->prefix . "§7Usage: §c/wm setdim <dimension> (world)");
                                    }
                                    break;
                                case "normal":
                                    if (empty($args[2])) {
                                        $s->getLevel()->setDimension(Level::DIMENSION_NORMAL);
                                        $s->sendMessage($this->prefix . "Level {$s->getLevel()->getName()} dimension changed to normal.");
                                    } else {
                                        $s->sendMessage($this->prefix . "§7Usage: §c/wm setdim <dimension> (world)");
                                    }
                                    break;
                                default:
                                    $s->sendMessage($this->prefix . "§7Usage: §c/wm setdim <dimension> (world)");
                                    break;
                            }
                        }
                        break;
                    case "setcrea":
                    case "setcreative":
                    case "setcreativeworld":
                    case "stc":
                        if (!$s->hasPermission("wm.cmd.stc")) {
                            $s->sendMessage(self::getPermissionMessage());
                            break;
                        }
                        else {
                            $this->config->set("creative", $s->getLevel()->getName());
                            $this->config->save();
                            $s->sendMessage($this->prefix."Level {$s->getLevel()->getName()} default gamemode changed to Creative.");
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
                $s->sendMessage($this->prefix . "§7Usage: §c/wm help");
            }
        }
    }
}

class GamemodeGuard extends PluginTask {

    public $prefix;

    /** @var  WorldManager */
    public $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun($currentTick) {
        $cfg = $this->plugin->config;
        $name = $cfg->get("creative");
        if($name !== "") {
            $level = $this->plugin->getServer()->getLevelByName($name);
            foreach ($this->plugin->getServer()->getLevels() as $levels) {
                if($levels->getName() == $name) {
                    foreach ($level->getPlayers() as $creativeplayers) {
                        $creativeplayers->setGamemode($creativeplayers::CREATIVE);
                    }
                }
                else {
                    foreach ($levels->getPlayers() as $p) {
                        if(!($p->hasPermission("wm.gm.creative"))) {
                            $p->setGamemode($p::SURVIVAL);
                        }
                    }
                }
            }
        }
    }
}
