<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2021  CzechPMDevs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace czechpmdevs\multiworld\command\subcommand;

use czechpmdevs\libpmform\response\FormResponse;
use czechpmdevs\libpmform\type\CustomForm;
use czechpmdevs\libpmform\type\SimpleForm;
use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\WorldUtils;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use function is_bool;

class ManageSubcommand implements SubCommand {

    public function executeSub(CommandSender $sender, array $args, string $name): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }

        $form = new SimpleForm("World Manager", "Select action", true);
        $form->addButton("Create a new world");
        $form->addButton("Delete world");
        $form->addButton("Update world game rules");
        $form->addButton("Show world info");
        $form->addButton("Load or unload world");
        $form->addButton("Teleport to the world");
        $form->addButton("Teleport player to the world");
        $form->addButton("Update spawn or lobby");

        $form->setCallback(function (Player $player, FormResponse $response): void {
            $customForm = new CustomForm("World Manager");

            switch ($response->getData()) {
                case 0:
                    $customForm->addLabel("Create world");
                    $customForm->addInput("Level name");
                    $customForm->addInput("Level seed");
                    $customForm->addDropdown("Generator", ["Normal", "Custom", "Nether", "End", "Flat", "Void", "SkyBlock"]);
                    $player->sendForm($customForm);
                    break;

                case 1:
                    $customForm->addLabel("Remove world");
                    $customForm->addDropdown("Level name", WorldUtils::getAllLevels());
                    $player->sendForm($customForm);
                    break;

                case 2:
                    $customForm->addLabel("Update level GameRules");
                    $rules = MultiWorld::getGameRules($player->getLevelNonNull())->getGameRules();
                    foreach ($rules as $rule => $value) {
                        if(is_bool($value)) {
                            $customForm->addToggle($rule, $value);
                        } else {
                            $customForm->addInput($rule);
                        }
                    }
                    $player->sendForm($customForm);
                    break;

                case 3:
                    $customForm->addLabel("Get information about the level");
                    $customForm->addDropdown("Levels", WorldUtils::getAllLevels());
                    $player->sendForm($customForm);
                    break;

                case 4:
                    $customForm->addLabel("Load/Unload world");
                    $customForm->addInput("Level to load §o(optional)");
                    $customForm->addInput("Level to unload §o(optional)");
                    $player->sendForm($customForm);
                    break;

                case 5:
                    $customForm->addLabel("Teleport to level");
                    $customForm->addDropdown("Level", WorldUtils::getAllLevels());
                    $player->sendForm($customForm);
                    break;

                case 6:
                    $customForm->addLabel("Teleport player to level");
                    $players = [];
                    foreach (Server::getInstance()->getOnlinePlayers() as $p) {
                        $players[] = $p->getName();
                    }
                    $customForm->addDropdown("Player", $players);
                    $customForm->addDropdown("Level", WorldUtils::getAllLevels());
                    $player->sendForm($customForm);
                    break;

                case 7:
                    $customForm->addLabel("Update level");
                    $customForm->addToggle("Update world spawn", true);
                    $customForm->addToggle("Update server lobby", false);
                    $player->sendForm($customForm);
                    break;
            }
        });

        $sender->sendForm($form);
    }

}