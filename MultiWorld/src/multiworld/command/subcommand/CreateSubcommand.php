<?php

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
