<?php

namespace MultiWorld\Command;

use MultiWorld\MultiWorld;
use MultiWorld\Util\ConfigManager;
use MultiWorld\Util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use SwagCore\SwagCore;

class MultiWorldCommand {

    /** @var  MultiWorld $plugin */
    public $plugin;

    /**
     * MultiWorldCommand constructor.
     * @param MultiWorld $plugin
     */
    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
    }
        /**
         * @param CommandSender $sender
         * @param Command $cmd
         * @param string $label
         * @param array $args
         * @return bool
         */
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
        if (!($sender instanceof Player)) {
            return false;
        }
        if ($cmd->getName() == "multiworld") {
            if (isset($args[0])) {
                switch (strtolower($args[0])) {
                    case "help":
                    case "?":
                        if (!$sender->hasPermission("mw.cmd.help")) {
                            $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                            return false;
                        }
                        $sender->sendMessage(LanguageManager::translateMessage("help-0") . "\n" .
                            LanguageManager::translateMessage("help-1") . "\n" .
                            LanguageManager::translateMessage("help-2") . "\n" .
                            LanguageManager::translateMessage("help-3") . "\n" .
                            LanguageManager::translateMessage("help-4") . "\n");
                        return true;
                    case "create":
                    case "add":
                    case "generate":
                        if (!$sender->hasPermission("mw.cmd.create")) {
                            $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                            return false;
                        }
                        if (empty($args[1])) {
                            $sender->sendMessage(SwagCore::getPrefix() . LanguageManager::translateMessage("create-usage"));
                            return false;
                        }
                        $this->plugin->bgenerator->generateLevel($args[1], $args[2], $args[3]);
                        $sender->sendMessage(SwagCore::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("create.generating")));
                        return false;
                    case "teleport":
                    case "tp":
                    case "move":
                        if (!$sender->hasPermission("mw.cmd.teleport")) {
                            $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                            return false;
                        }
                        if (empty($args[1])) {
                            $sender->sendMessage(SwagCore::getPrefix() . LanguageManager::translateMessage("teleport-usage"));
                            return false;
                        }
                        if (!Server::getInstance()->isLevelGenerated($args[1])) {
                            $sender->sendMessage(SwagCore::getPrefix() . LanguageManager::translateMessage("teleport-levelnotexists"));
                            return false;
                        }
                        if (!Server::getInstance()->isLevelLoaded($args[1])) {
                            Server::getInstance()->loadLevel($args[1]);
                            $this->plugin->getLogger()->debug(SwagCore::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("teleport-load")));
                        }
                        if (isset($args[2])) {
                            $player = Server::getInstance()->getPlayer($args[2]);
                            if ($player->isOnline()) {
                                $player->teleport(Server::getInstance()->getLevelByName($args[1])->getSafeSpawn(), 0, 0);
                                $player->sendMessage(SwagCore::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("teleport-done-1")));
                                $sender->sendMessage(SwagCore::getPrefix() . str_replace("%1", $args[1], str_replace("%2", $player->getName(), LanguageManager::translateMessage("teleport-done-2"))));
                                return false;
                            } else {
                                $sender->sendMessage(SwagCore::getPrefix() . LanguageManager::translateMessage("teleport-playernotexists"));
                                return false;
                            }
                        } else {
                            $sender->teleport(Server::getInstance()->getLevelByName($args[1])->getSafeSpawn(), 0, 0);
                            $sender->sendMessage(SwagCore::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("teleport-done-1")));
                        }
                        break;
                    case "import":
                        if (!$sender->hasPermission("mw.cmd.import")) {
                            $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                            return false;
                        }
                        if (empty($args[1])) {
                            $sender->sendMessage(SwagCore::getPrefix() . LanguageManager::translateMessage("import-usage"));
                            return false;
                        }
                        $zipPath = ConfigManager::getDataPath() . "levels/{$args[1]}.zip";
                        if (!file_exists($zipPath)) {
                            $sender->sendMessage(SwagCore::getPrefix() . LanguageManager::translateMessage("import-zipnotexists"));
                            return false;
                        }
                        $zip = new \ZipArchive;
                        $zip->open($zipPath);
                        $zip->extractTo(ConfigManager::getDataPath() . "worlds/");
                        $zip->close();
                        unset($zip);
                        Server::getInstance()->loadLevel($args[1]);
                        $sender->sendMessage(SwagCore::getPrefix() . LanguageManager::translateMessage("import-done"));
                        return false;
                    case "list":
                    case "ls":
                    case "levels":
                    case "worlds":
                        if (!$sender->hasPermission("mw.cmd.list")) {
                            $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                            return false;
                        }
                        $list = scandir(ConfigManager::getDataPath() . "worlds");
                        unset($list[0]);
                        unset($list[1]);
                        $list = implode(", ", $list);
                        $sender->sendMessage(SwagCore::getPrefix() . str_replace("%1", $list, LanguageManager::translateMessage("list-done")));
                        return false;
                    case "load":
                        if (!$sender->hasPermission("mw.cmd.load")) {
                            $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                            return false;
                        }
                        if (empty($args[1])) {
                            $sender->sendMessage(SwagCore::getPrefix() . LanguageManager::translateMessage("load-usage"));
                            return false;
                        }
                        if (!Server::getInstance()->isLevelGenerated($args[1])) {
                            $sender->sendMessage(SwagCore::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("load-levelnotexists")));
                            return false;
                        }
                        Server::getInstance()->loadLevel($args[1]);
                        $sender->sendMessage(SwagCore::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("load-done")));
                        break;
                    case "unload":
                        if (!$sender->hasPermission("mw.cmd.unload")) {
                            $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                            return false;
                        }
                        if (empty($args[1])) {
                            $sender->sendMessage(SwagCore::getPrefix() . LanguageManager::translateMessage("unload-usage"));
                            return false;
                        }
                        if (!Server::getInstance()->isLevelGenerated($args[1])) {
                            $sender->sendMessage(SwagCore::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("unload-levelnotexists")));
                            return false;
                        }
                        if (!Server::getInstance()->isLevelLoaded($args[1])) {
                            $sender->sendMessage(SwagCore::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("unload-unloaded")));
                            return false;
                        }
                        Server::getInstance()->unloadLevel(Server::getInstance()->getLevelByName($args[1]));
                        $sender->sendMessage(SwagCore::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage("unload-done")));
                        return false;
                }
            } else {
                if (!$sender->hasPermission("mw.cmd.help")) {
                    $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                } else {
                    $sender->sendMessage(SwagCore::getPrefix() . LanguageManager::translateMessage("default-usage"));
                }
            }
        }

    }

}