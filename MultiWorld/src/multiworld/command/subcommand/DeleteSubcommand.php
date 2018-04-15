<?php

declare(strict_types=1);

namespace multiworld\command\subcommand;

use multiworld\command\MultiWorldCommand;
use multiworld\MultiWorld;
use multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;

class DeleteSubcommand extends MultiWorldCommand implements SubCommand {

    public function __construct(){}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(empty($args[0])) {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("delete-usage"));
            return;
        }
        if(!$this->getServer()->isLevelGenerated($args[0]) || !file_exists($this->getServer()->getDataPath()."worlds/{$args[0]}")) {
            $sender->sendMessage(MultiWorld::getPrefix() . str_replace("%1", $args[0], LanguageManager::translateMessage("delete-levelnotexists")));
            return;
        }

        if(!$this->getServer()->getDefaultLevel()->getFolderName() == $this->getServer()->getLevelByName($args[0])->getFolderName()) {
            $sender->sendMessage("§cCould not remove default level!");
            return;
        }

        $files = $this->removeLevel($args[0]);

        $msg = LanguageManager::translateMessage("delete-done");
        $msg = str_replace("%1", $files, $msg);

        $sender->sendMessage(MultiWorld::getPrefix() . $msg);
    }

    /**
     * @param string $name
     * @return int $files
     */
    public function removeLevel(string $name): int {
        if($this->getServer()->isLevelLoaded($name)) {
            $level = $this->getServer()->getLevelByName($name);

            if(count($level->getPlayers()) > 0) {
                foreach ($level->getPlayers() as $player) {
                    $player->teleport($this->getServer()->getDefaultLevel()->getSpawnLocation());
                }
            }

            $this->getServer()->unloadLevel($level, true);
        }

        return $this->removeDir($this->getServer()->getDataPath() . DIRECTORY_SEPARATOR . "worlds" . DIRECTORY_SEPARATOR . $name);
    }

    /**
     * @param string $path
     * @return int
     */
    public function removeFile(string $path): int {
        unlink($path); return 1;
    }

    /**
     * @param string $dirPath
     * @return int
     */
    public function removeDir(string $dirPath): int {
        $files = 1;
        if(basename($dirPath) == "." || basename($dirPath) == "..") {
            return 0;
        }
        foreach (scandir($dirPath) as $item) {
            if($item != "." || $item != "..") {
                if(is_dir($dirPath . DIRECTORY_SEPARATOR . $item)) {
                    $files += $this->removeDir($dirPath . DIRECTORY_SEPARATOR . $item);
                }
                if(is_file($dirPath . DIRECTORY_SEPARATOR . $item)) {
                    $files += $this->removeFile($dirPath . DIRECTORY_SEPARATOR . $item);
                }
            }

        }
        rmdir($dirPath);
        return $files;
    }
}
