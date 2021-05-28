<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2020  CzechPMDevs
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

class SimpleForm implements Form {

    public const FORM_MENU = 0;

    /** @var mixed[] */
    public array $data = [];
    /** @var int */
    public int $mwId;

    public function __construct(string $title = "TITLE", string $content = "Content") {
        $this->data["type"] = "form";
        $this->setTitle($title);
        $this->setContent($content);
    }

    public function setTitle(string $text): void {
        $this->data["title"] = $text;
    }

    public function setContent(string $text): void {
        $this->data["content"] = $text;
    }

    public function addButton(string $text): void {
        $this->data["buttons"][] = ["text" => $text];
    }

    public function handleResponse(Player $player, $data): void {
        MultiWorld::getInstance()->formManager->handleFormResponse($player, $data, $this);
    }

    public function jsonSerialize() {
        return $this->data;
    }
}