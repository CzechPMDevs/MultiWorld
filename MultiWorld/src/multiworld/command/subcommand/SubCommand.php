<?php

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