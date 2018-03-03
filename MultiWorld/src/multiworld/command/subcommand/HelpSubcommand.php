<?php

declare(strict_types=1);

namespace multiworld\command\subcommand;

use multiworld\command\MultiWorldCommand;
use multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;

/**
 * Class HelpSubcommand
 * @package multiworld\command\subcommand
 */
class HelpSubcommand extends MultiWorldCommand implements SubCommand {

    /**
     * HelpSubcommand constructor.
     */
    public function __construct(){}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(empty($args[0])) {
            $sender->sendMessage($this->getHelpPage(1));
            return;
        }

        if(!is_numeric($args[0])) {
            $sender->sendMessage($this->getHelpPage(1));
            return;
        }

        $sender->sendMessage($this->getHelpPage((int)$args[0]));
    }

    public function getHelpPage(int $page): string {
        $title = LanguageManager::translateMessage("help");

        $title = str_replace("%page", $page, $title);
        $title = str_replace("%max", "2", $title);

        $text = $title;

        switch ($page) {
            default:
                $text .= PHP_EOL.LanguageManager::translateMessage("help-1");
                $text .= PHP_EOL.LanguageManager::translateMessage("help-2");
                $text .= PHP_EOL.LanguageManager::translateMessage("help-3");
                $text .= PHP_EOL.LanguageManager::translateMessage("help-4");
                $text .= PHP_EOL.LanguageManager::translateMessage("help-5");
                break;

            case 2:
                $text .= PHP_EOL.LanguageManager::translateMessage("help-6");
                break;
        }
        return $text;
    }
}
