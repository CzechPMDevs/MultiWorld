<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2019  CzechPMDevs
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

use czechpmdevs\multiworld\api\WorldManagementAPI;
use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;

/**
 * Class CreateSubcommand
 * @package czechpmdevs\multiworld\command\subcommand
 */
class CreateSubcommand implements SubCommand {

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(!isset($args[0])) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "create-usage"));
            return;
        }

        if(MultiWorld::getInstance()->getServer()->isLevelGenerated($args[0])) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "create-exists", [$args[0]]));
            return;
        }

        $seed = 0;
        if(isset($args[1]) && is_numeric($args[1])) {
            $seed = (int) $args[1];
        }

        $generatorName = "normal";
        $generator = null;

        if(isset($args[2])) {
            $generatorName = $args[2];
        }

        switch (strtolower($generatorName)) {
            case "normal":
            case "classic":
            case "basic":
                $generator = WorldManagementAPI::GENERATOR_NORMAL;
                $generatorName = "Normal";
                break;
            case "custom": // todo rename that to normal
                $generator = WorldManagementAPI::GENERATOR_NORMAL_CUSTOM;
                $generatorName = "Custom";
                break;
            case "flat":
            case "superflat":
                $generator = WorldManagementAPI::GENERATOR_FLAT;
                $generatorName = "Flat";
                break;
            case "nether":
            case "hell":
                $generator = WorldManagementAPI::GENERATOR_HELL;
                $generatorName = "Nether";
                break;
            case "ender":
            case "end":
                $generator = WorldManagementAPI::GENERATOR_ENDER;
                $generatorName = "End";
                break;
            case "void":
                $generator = WorldManagementAPI::GENERATOR_VOID;
                $generatorName = "Void";
                break;
            case "skyblock":
            case "sb":
            case "sky":
                $generator = WorldManagementAPI::GENERATOR_SKYBLOCK;
                $generatorName = "SkyBlock";
                break;
            case "nether_old":
                $generator = WorldManagementAPI::GENERATOR_HELL_OLD;
                $generatorName = "Old Nether";
                break;
            default:
                $generator = WorldManagementAPI::GENERATOR_NORMAL;
                $generatorName = "Normal";
                break;
        }

        WorldManagementAPI::generateLevel($args[0], $seed, $generator);
        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($sender, "create-done", [$args[0], $seed, $generatorName]));
    }
}
