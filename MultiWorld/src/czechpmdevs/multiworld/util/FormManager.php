<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\util;

use czechpmdevs\multiworld\api\WorldGameRulesAPI;
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
                $customForm = new CustomForm("World Manager");
                $customForm->mwId = 2;
                $customForm->addLabel("Update level GameRules");
                $rules = WorldGameRulesAPI::getLevelGameRules($player->getLevel());
                var_dump($rules);
                foreach ($rules as $rule => [1 => $value]) {
                    var_dump($rule);
                    var_dump($value);
                    $customForm->addToggle((string)$rule, $value);
                }
                $player->sendForm($customForm);
                break;
            case 3:
                $customForm = new CustomForm("World Manager");
                $customForm->mwId = 3;
                $customForm->addLabel("Get information about the level");
                $levels = [];
                foreach ($this->plugin->getServer()->getLevels() as $level) {
                    $levels[] = $level->getFolderName();
                }

                $customForm->addDropdown("Levels", $levels);
                $player->sendForm($customForm);
                break;
            case 4:
                $customForm = new CustomForm("WorldManager");
                $customForm->mwId = 4;
                $customForm->addLabel("Load/Unload world");
                $customForm->addInput("Level to load §o(optional)");
                $customForm->addInput("Level to unload §o(optional)");
                $player->sendForm($customForm);
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
                if($data[1] === null) {
                    LanguageManager::getMsg($player, "forms.invalid");
                }
                $name = (string)$data[1];
                $seed = (int)$data[2];
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
                $this->plugin->getServer()->dispatchCommand($player, "mw create $name $seed $genName");
                break;
            case 1:
                $this->plugin->getServer()->dispatchCommand($player, "mw delete {$data[1]}");
                break;
            case 2:
                array_shift($data);
                $gameRules = array_keys(WorldGameRulesAPI::getLevelGameRules($player->getLevel()));
                foreach ($data as $i => $v) {
                    $this->plugin->getServer()->dispatchCommand($player, "gamerule {$gameRules[$i]} " . ((bool)$v ? "true" : "false"));
                }
                break;
            case 3:
                $levels = [];
                foreach ($this->plugin->getServer()->getLevels() as $level) {
                    $levels[] = $level;
                }
                $this->plugin->getServer()->dispatchCommand($player, "mw info " . $levels[(int)$data[1]]->getFolderName());
                break;
            case 4:
                if($data[1] != "") {
                    $this->plugin->getServer()->dispatchCommand($player, "mw load {$data[1]}");
                }
                if($data[2] != "") {
                    $this->plugin->getServer()->dispatchCommand($player, "mw unload {$data[2]}");
                }
                break;
            case 5:
                break;
        }
    }
}