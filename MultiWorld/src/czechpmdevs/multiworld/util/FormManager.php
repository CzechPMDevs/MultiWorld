<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\util;

use czechpmdevs\multiworld\api\WorldManagementAPI;
use czechpmdevs\multiworld\form\CustomForm;
use czechpmdevs\multiworld\MultiWorld;
use pocketmine\form\Form;
use pocketmine\level\generator\normal\Normal;
use pocketmine\Player;

/**
 * Class FormManager
 * @package czechpmdevs\multiworld\util
 */
class FormManager {

    /** @var MultiWorld $plugin */
    public $plugin;

    /**
     * FormManager constructor.
     * @param MultiWorld $plugin
     */
    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param Player $player
     * @param mixed $data
     * @param Form $form
     */
    public function handleFormResponse(Player $player, $data, Form $form) {
        if($data === null) return;
        switch ($data) {
            case 0:
                $customForm = new CustomForm("World Manager");
                $customForm->mwId = 0;
                $customForm->addLabel("Create world");
                $customForm->addInput("Level name");
                $customForm->addInput("Level seed");
                $customForm->addDropdown("Generator", ["Normal", "Nether", "End", "Flat", "Void", "SkyBlock"]);
                $player->sendForm($customForm);
                break;
            case 1:
                $customForm = new CustomForm("World Manager");
                $customForm->mwId = 1;
                $customForm->addLabel("Remove world");
                $customForm->addInput("Level name");
                $player->sendForm($customForm);
                break;
            case 2:
                $customForm = new CustomForm("WorldManager");
                $customForm->mwId = 2;
                $customForm->addLabel("Load/Unload world");
                $customForm->addInput("Level to load §o(optional)");
                $customForm->addInput("Level to unload §o(optional)");
        }
    }

    /**
     * @param Player $player
     * @param mixed $data
     * @param CustomForm $form
     */
    public function handleCustomFormResponse(Player $player, $data, CustomForm $form) {
        switch ($form->mwId) {
            case 0:
                var_dump($data);
                $name = $data[1];
                $seed = $data[2];
                $gen = (int)$data[3];
                $genName = "Normal";
                switch ($gen) {
                    case WorldManagementAPI::GENERATOR_NORMAL:
                        $genName = "Normal";
                        break;
                    case WorldManagementAPI::GENERATOR_HELL:
                        $genName = "Hell";
                        break;
                    case WorldManagementAPI::GENERATOR_ENDER:
                        $genName = "End";
                        break;
                    case WorldManagementAPI::GENERATOR_VOID:
                        $genName = "Void";
                        break;
                    case WorldManagementAPI::GENERATOR_SKYBLOCK:
                        $genName = "SkyBlock";
                        break;
                    case WorldManagementAPI::GENERATOR_HELL_OLD:
                        $genName = "Nether_Old";
                        break;
                }
                $this->plugin->getServer()->dispatchCommand($player, "mw create {$name} {$seed} {$genName}");
                break;
            case 1:
                $this->plugin->getServer()->dispatchCommand($player, "mw delete {$data[1]}");
                break;
            case 2:
                if($data[1] != "") {
                    $this->plugin->getServer()->dispatchCommand($player, "mw load {$data[1]}");
                }
                if($data[2] != "") {
                    $this->plugin->getServer()->dispatchCommand($player, "mw unload {$data[2]}");
                }
                break;
            case 3:
                break;
        }
    }
}