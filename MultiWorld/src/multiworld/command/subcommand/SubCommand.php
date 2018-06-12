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

use pocketmine\command\CommandSender;

/**
 * Interface SubCommand
 * @package multiworld\command\subcommand
 */
interface SubCommand {

    /**
     * SubCommand constructor.
     */
    public function __construct();

    /**
     * @api
     *
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     *
     * @return mixed
     */
    public function executeSub(CommandSender $sender, array $args, string $name);
}