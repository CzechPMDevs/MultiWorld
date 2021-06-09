<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\command\subcommand;

use czechpmdevs\multiworld\form\SimpleForm;
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

        $form = new SimpleForm("World Manager", "Select action");
        $form->mwId = 0;
        $form->addButton("Create world");
        $form->addButton("Delete world");
        $form->addButton("Manage world GameRules");
        $form->addButton("Get information about worlds");
        $form->addButton("Load or unload world");
        $form->addButton("Teleport to level");
        $form->addButton("Teleport player to level");
        $form->addButton("Update lobby/spawn position");

        $sender->sendForm($form);
    }

}