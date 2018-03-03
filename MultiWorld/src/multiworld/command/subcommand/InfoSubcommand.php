<?php

declare(strict_types=1);

namespace multiworld\command\subcommand;
use multiworld\command\MultiWorldCommand;
use multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\Player;

/**
 * Class InfoSubcommand
 * @package multiworld\command\subcommand
 */
class InfoSubcommand extends MultiWorldCommand implements SubCommand {

    /**
     * InfoSubcommand constructor.
     */
    public function __construct(){}

    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }
        $sender->sendMessage($this->getInfoMsg($sender->getLevel()));
    }

    public function getInfoMsg(Level $level): string {
        $name = $level->getName();
        $folderName = $level->getFolderName();
        $seed = $level->getSeed();
        $players = count($level->getPlayers());
        $generator = $level->getProvider()->getGenerator();
        $time = $level->getTime();

        $msg = LanguageManager::translateMessage("info");
        $msg .= PHP_EOL.LanguageManager::translateMessage("info-name");
        $msg .= PHP_EOL.LanguageManager::translateMessage("info-folderName");
        $msg .= PHP_EOL.LanguageManager::translateMessage("info-players");
        $msg .= PHP_EOL.LanguageManager::translateMessage("info-generator");
        $msg .= PHP_EOL.LanguageManager::translateMessage("info-seed");
        $msg .= PHP_EOL.LanguageManager::translateMessage("info-time");

        $msg = str_replace
        ([
            "%1",
            "%2",
            "%3",
            "%4",
            "%5",
            "%6"
        ], [
            $name,
            $folderName,
            $players,
            $generator,
            $seed,
            $time
        ], $msg);

        return $msg;
    }
}