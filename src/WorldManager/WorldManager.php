<?php

namespace WorldManager;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\level\generator\Generator;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class WorldManager extends PluginBase implements Listener {

    public $prefix;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("§aEnabling WorldManager...");
        $this->loadConfig();
    }

    public function loadConfig() {
        if(!is_file($this->getDataFolder()."/config.yml")) {
            @mkdir($this->getDataFolder());
            $cfg = new Config($this->getDataFolder()."/config.yml", Config::YAML);
            $cfg->set("prefix", "§2[§WorldManager§2]");
            $cfg->save();
        }
        $cfg = new Config($this->getDataFolder()."/config.yml", Config::YAML);
        $this->prefix = "{$cfg->get("prefix")} ";
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

    public function onCommand(CommandSender $s, Command $cmd, $label, array $args) {
        if($cmd->getName() == "worldmanager" && $s instanceof Player) {
            if(isset($args[0])) {
                switch ($args[0]) {
                    case "help":
                        if(empty($args[1])) {
                            $s->sendMessage("§3---== §a[WorldManager] §3==---§r§f\n".
                                            "§9/wm create §6Create new World\n".
                                            "§9/wm setspawn §6Sets world spawn\n".
                                            "§9/wm setdefault §6Set world as the default level\n".
                                            "§9/wm sethub §6Sets server hub\n".
                                            "§9/wm list §6Displays all worlds\n".
                                            "§9/wm tp §6Moves you to the world\n".
                                            "§9/wm genlist §6Displays all generators\n".
                                            "§9/wm load §6Retrieves world\n".
                                            "§9/wm unload §6Unload wold\n".
                                            "§9/wm delete §6Delete world\n".
                                            "§9/wm info §6Info about level\n".
                                            "§9/wm rename §6 Rename world");
                        }
                        break;
                    case "create":
                    case "new":
                    case "add":
                        if(!$s->hasPermission("wm.cmd.create")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }
                        if(count($args) >= 4) {
                            if(!is_numeric($args[3])) {
                                $s->sendMessage($this->prefix."§cSeed must be formatted numbers!");
                            }
                            else {
                                $this->createWorld($s,$args[1],strtolower($args[2]),$args[3]);
                            }
                        }
                        else {
                            $s->sendMessage($this->prefix."§7Usage: §c/wm <create | new | add> <name> <generator> <seed>");
                        }

                        break;
                    case "setspawn":
                    case "setworldspawn":
                        if(!$s->hasPermission("wm.cmd.setspawn")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }
                        if(empty($args[1])) {
                            $s->getLevel()->setSpawnLocation($s);
                            $s->sendMessage($this->prefix."§aSpawn location updated!");
                        }
                        break;
                    case "setdefault":
                        if(!$s->hasPermission("wm.cmd.setdefault")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }
                        if(empty($args[1])) {
                            $this->getServer()->setDefaultLevel($s->getLevel());
                            $s->sendMessage($this->prefix."§aDefault level updated!");
                        }
                        else {
                            $this->getServer()->setDefaultLevel($this->getServer()->getLevelByName($args[1]));
                            $s->sendMessage($this->prefix."§aDefault level updated!");
                        }
                        break;
                    case "sethub":
                    case "setlobby":
                        if(!$s->hasPermission("wm.cmd.sethub")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }
                        if(empty($args[1])) {
                            $this->getServer()->setDefaultLevel($s->getLevel());
                            $s->getLevel()->setSpawnLocation($s);
                            $s->sendMessage($this->prefix."§aLobby position updated!");
                        }

                        break;
                    case "list":
                    case "ls":
                        if(!$s->hasPermission("wm.cmd.list")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }
                        $s->sendMessage("§2All Levels:");
                        foreach ($this->getServer()->getLevels() as $level) {
                            $s->sendMessage("§7- §a{$level->getName()}");
                        }
                        break;
                    case "teleport":
                    case "tp":
                        if(!$s->hasPermission("wm.cmd.tp")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }
                        if(!empty($args[1]) && empty($args[2])) {
                            if($this->getServer()->getLevelByName($args[1]) !== null) {
                                $this->getServer()->loadLevel($args[1]);
                                $s->teleport($this->getServer()->getLevelByName($args[1])->getSpawnLocation());
                                $s->sendMessage($this->prefix."§aYou've been teleported to the world {$args[1]}.");
                            }
                            else {
                                $s->sendMessage($this->prefix."§cWorld {$args[1]} does not exists.");
                            }
                        }
                        elseif(!empty($args[1]) && !empty($args[2])) {
                            if ($this->getServer()->getLevelByName($args[1]) !== null) {
                                if ($this->getServer()->getPlayer($args[2]) instanceof Player) {
                                    $p = $this->getServer()->getPlayer($args[2]);
                                    $p->teleport($this->getServer()->getLevelByName($args[1])->getSpawnLocation());
                                    $p->sendMessage($this->prefix . "§aYou've been teleported to the world {$args[1]}.");
                                } else {
                                    $s->sendMessage($this->prefix . "§cPlayer {$args[1]} is not online!");
                                }
                            } else {
                                $s->sendMessage($this->prefix . "§cWorld {$args[1]} does not exists.");
                            }

                        }
                        break;
                    case "generators":
                    case "genlist":
                    case "generatorlist":
                        if(!$s->hasPermission("wm.cmd.genlist")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }
                        $s->sendMessage($this->prefix."§2Generators: §aDefault, Nether, Flat, Void");
                        break;
                    case "load":
                        if(!$s->hasPermission("wm.cmd.load")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }

                        if(isset($args[1])) {
                            if(file_exists($this->getDataFolder()."worlds/{$args[1]}")) {
                                $this->getServer()->loadLevel($args[1]);
                                $s->sendMessage($this->prefix."§aThe level {$args[1]} was loaded in background");
                            }
                            else {
                                $s->sendMessage($this->prefix."§cWorld does not exists!");
                            }
                        }
                        else {
                            $s->sendMessage($this->prefix."§7Usage: §c/wm load <level>");
                        }
                        break;
                    case "unload":
                        if(!$s->hasPermission("wm.cmd.unload")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }

                        if(isset($args[1])) {
                            if($this->getServer()->getLevelByName($args[1]) instanceof Level) {
                                $this->getServer()->unloadLevel($this->getServer()->getLevelByName($args[1]), true);
                            }
                        }
                        else {
                            $s->sendMessage($this->prefix."§7Usage: §c/wm unload <level>");
                            $s->sendMessage($this->prefix."§aThe level {$args[1]} was loaded in background");
                        }
                        break;
                    case "delete":
                    case "remove":
                    case "del":
                    case "rm":
                        if(!$s->hasPermission("wm.cmd.delete")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }

                        if(!empty($args[1])) {
                            if($this->getServer()->getLevelByName($args[1]) instanceof Level) {
                                foreach ($this->getServer()->getLevelByName($args[1])->getPlayers() as $pl) {
                                    $pl->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
                                }
                                rmdir($this->getServer()->getDataPath()."worlds/{$args[1]}");
                                $this->getLogger()->debug($this->prefix."§aWorld deleted sucessfuly");
                                $this->getServer()->shutdown(true);
                            }
                        }
                        else {
                            $s->sendMessage($this->prefix."§7Usage:§c /wm delete <level>");
                        }
                        break;
                    case "info":
                        if(!$s->hasPermission("wm.cmd.info")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }

                        if(!empty($args[1]) && $this->getServer()->getLevelByName($args[1]) instanceof Level) {
                            $level = $this->getServer()->getLevelByName($args[1]);
                            $i1 = count($level->getPlayers());
                            $i2 = $level->getDimension();
                            $i3 = $level->getSeed();
                            $i4 = $level->getTime();
                            $s->sendMessage("§3---== §6{$level->getName()} §3==---\n".
                                            "§9Players: §e{$i1}\n".
                                            "§9Dimension: §e{$i2}\n".
                                            "§9Seed: §e{$i3}\n".
                                            "§9Time: §e{$i4}");
                        }
                        else {
                            $s->sendMessage($this->prefix."§7Usage: §c/wm info <level>");
                        }
                        break;
                    case "rename":
                        if(!$s->hasPermission("wm.cmd.rename")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }

                        if(!empty($args[1]) && !empty($args[2])) {
                            if($this->getServer()->getLevelByName($args[1]) instanceof Level) {
                                rename($this->getServer()->getDataPath()."worlds/{$args[1]}",$this->getServer()->getDataPath()."worlds/{$args[2]}");
                                $this->getServer()->reload();
                                $s->sendMessage($this->prefix."§aWorld {$args[1]} was renamed to {$args[2]}!");
                            }
                            else {
                                $s->sendMessage($this->prefix."§cWorld {$args[1]} does not exists");
                            }
                        }
                        else {
                            $s->sendMessage($this->prefix."§7Usage: §c/wm rename <oldname> <newname>");
                        }
                        break;
                    default:
                        if(!$s->hasPermission("wm.cmd.rename")) {
                            $s->sendMessage($cmd->getPermissionMessage());
                            break;
                        }
                        $s->sendMessage($this->prefix."§7Usage: §c/wm help");
                        break;
                }
            }
        }
    }
}
