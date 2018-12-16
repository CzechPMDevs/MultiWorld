<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\form;

use czechpmdevs\multiworld\MultiWorld;
use pocketmine\Player;
use pocketmine\Server;

/**
 * Class Form
 * @package czechpmdevs\multiworld\form
 */
class Form implements \pocketmine\form\Form {

    /** @var array $formData */
    public $data = [];

    /** @var mixed $responseData */
    public $responseData;

    /**
     * Form constructor.
     * @param string $title
     * @param string $content
     */
    public function __construct(string $title = "TITLE", string $content = "Content") {
        $this->data["type"] = "form";
        $this->setTitle($title);
        $this->setContent($content);
    }

    /**
     * @param string $text
     */
    public function setTitle(string $text) {
        $this->data["title"] = $text;
    }

    /**
     * @param string $text
     */
    public function setContent(string $text) {
        $this->data["content"] = $text;
    }


    /**
     * @param string $text
     */
    public function addButton(string $text) {
        $this->data["buttons"][] = ["text" => $text];
    }

    /**
     * @param Player $player
     * @param mixed $data
     */
    public function handleResponse(Player $player, $data): void {
        MultiWorld::getInstance()->formManager->handleFormResponse($player, $data, $this);
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getResponse() {
        return $this->data;
    }
}