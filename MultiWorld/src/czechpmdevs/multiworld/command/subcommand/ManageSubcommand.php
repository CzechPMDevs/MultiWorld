<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\command\subcommand;

use czechpmdevs\multiworld\form\Form;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class ManageSubcommand
 * @package czechpmdevs\multiworld\command\subcommand
 */
class ManageSubcommand implements SubCommand {

    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }

        $form = new Form("World Manager", "Select action");
        $form->addButton("Create world");
        $form->addButton("Delete world");
        $form->addButton("Load or unload world");
        $form->addButton("Get information about worlds");

        $sender->sendForm($form);
    }

}