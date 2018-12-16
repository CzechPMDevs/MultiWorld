<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\util;

use czechpmdevs\multiworld\api\WorldManagementAPI;
use czechpmdevs\multiworld\form\CustomForm;
use czechpmdevs\multiworld\MultiWorld;
use pocketmine\form\Form;
use pocketmine\Player;

/**
 * Class FormManager
 * @package czechpmdevs\multiworld\util
 */
class FormManager {



    /** @var MultiWorld $plugin */
    public $plugin;

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
                $customForm->addInput("Level name");
                $customForm->addInput("Level seed");
                $customForm->addDropdown("Generator", ["Normal", "Nether", "End", "Flat", "Void", "SkyBlock"]);
                $player->sendForm($customForm);
                break;
        }
    }

    /**
     * @param Player $player
     * @param mixed $data
     * @param CustomForm $form
     */
    public function handleCustomFormResponse(Player $player, $data, CustomForm $form) {
        $name = $data[0] !== null ? $data[0] : "MyWorld";
        $seed = (int)$data[1];
        $player->sendMessage(MultiWorld::getPrefix() . LanguageManager::getMsg($player, "create-done", [$name, $seed, "???"]));
        switch ($form->mwId) {
            case 0:
                WorldManagementAPI::generateLevel($name, $seed, (int)$data[2]);
                break;
        }
    }
}