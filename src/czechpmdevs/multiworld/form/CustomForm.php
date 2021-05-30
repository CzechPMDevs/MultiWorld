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

namespace czechpmdevs\multiworld\form;

use czechpmdevs\multiworld\MultiWorld;
use pocketmine\form\Form;
use pocketmine\Player;

class CustomForm implements Form {

    public const FORM_CREATE = 1;

    /** @var mixed[] */
    public array $data = [];
    /** @var int */
    public int $mwId;

    public function __construct(string $title = "") {
        $this->data["type"] = "custom_form";
        $this->data["title"] = $title;
        $this->data["content"] = [];
    }

    public function addInput(string $text): void {
        $this->data["content"][] = ["type" => "input", "text" => $text];
    }

    public function addLabel(string $text): void {
        $this->data["content"][] = ["type" => "label", "text" => $text];
    }


    public function addToggle(string $text, ?bool $default = null): void {
        if ($default !== null) {
            $this->data["content"][] = ["type" => "toggle", "text" => $text, "default" => $default];
            return;
        }
        $this->data["content"][] = ["type" => "toggle", "text" => $text];
    }

    /**
     * @param string[] $options
     */
    public function addDropdown(string $text, array $options): void {
        $this->data["content"][] = ["type" => "dropdown", "text" => $text, "options" => $options];
    }

    public function handleResponse(Player $player, $data): void {
        MultiWorld::getInstance()->formManager->handleCustomFormResponse($player, $data, $this);
    }

    public function jsonSerialize() {
        return $this->data;
    }
}