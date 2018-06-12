<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018  CzechPMDevs
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

namespace multiworld\command\subcommand;

use multiworld\command\MultiWorldCommand;
use multiworld\generator\ender\EnderGenerator;
use multiworld\generator\skyblock\SkyBlockGenerator;
use multiworld\generator\void\VoidGenerator;
use multiworld\MultiWorld;
use multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\level\generator\Flat;
use pocketmine\level\generator\hell\Nether;
use pocketmine\level\generator\normal\Normal;

/**
 * Class CreateSubcommand
 * @package multiworld\command\subcommand
 */
class CreateSubcommand extends MultiWorldCommand implements SubCommand {

    public function __construct(){}

    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(empty($args[0])) {
            $sender->sendMessage(LanguageManager::translateMessage("create-usage"));
            return;
        }

        $seed = 0;
        if(isset($args[1]) && is_numeric($args[1])) {
            $seed = intval($args[1]);
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
                $generator = Normal::class;
                $generatorName = "Normal";
                break;
            case "flat":
            case "superflat":
                $generator = Flat::class;
                $generatorName = "Flat";
                break;
            case "nether":
            case "hell":
                $generator = Nether::class;
                $generatorName = "Nether";
                break;
            case "ender":
            case "end":
                $generator = EnderGenerator::class;
                $generatorName = "End";
                break;
            case "void":
                $generator = VoidGenerator::class;
                $generatorName = "Void";
                break;
            case "skyblock":
            case "sb":
            case "sky":
                $generator = SkyBlockGenerator::class;
                $generatorName = "SkyBlock";
                break;
            default:
                $generator = Normal::class;
                $generatorName = "Normal";
                break;
        }

        $this->getPlugin()->getServer()->generateLevel($args[0], $seed, $generator);

        $msg = LanguageManager::translateMessage("create-done");
        $msg = str_replace("%1", $args[0], $msg);
        $msg = str_replace("%2", $seed, $msg);
        $msg = str_replace("%3", $generatorName, $msg);


        $sender->sendMessage(MultiWorld::getPrefix().$msg);
    }
}
