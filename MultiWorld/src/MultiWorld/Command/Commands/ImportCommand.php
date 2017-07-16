<?php

namespace MultiWorld\Command\Commands;

use MultiWorld\Command\MultiWorldCommand;
use MultiWorld\MultiWorld;
use MultiWorld\Util\ConfigManager;
use MultiWorld\Util\LanguageManager;
use pocketmine\Player;
use pocketmine\Server;

class ImportCommand {

    /** @var  MultiWorld */
    public $plugin;

    /** @var  MultiWorldCommand */
    public $command;

    public function __construct(MultiWorld $plugin, MultiWorldCommand $command) {
        $this->plugin = $plugin;
        $this->command = $command;
    }

    /**
     * @param Player $sender
     * @param array $args
     * @return bool;
     */
    public function execute(Player $sender, array $args) {
        if(isset($args[1])) {
            $zipPath = ConfigManager::getDataPath()."levels/{$args[1]}.zip";
            if(file_exists($zipPath)) {
                $zip = new \ZipArchive;
                $zip->open($zipPath);
                $zip->extractTo(ConfigManager::getDataPath()."worlds/");
                $zip->close();
                unset($zip);
                Server::getInstance()->loadLevel($args[1]);
                $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("import-done"));
            }
            else {
                $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("import-zipnotexists"));
            }
        }
        else {
            $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("import-usage"));
        }



    }
}