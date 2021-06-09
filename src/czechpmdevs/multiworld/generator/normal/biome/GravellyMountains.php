<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\generator\normal\biome;

use pocketmine\block\Block;

/**
 * Class GravellyMountains
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class GravellyMountains extends Mountains {

    public function __construct() {
        parent::__construct();

        $this->setElevation(75, 120);
        $this->setGroundCover([
            Block::get(Block::GRAVEL),
            Block::get(Block::GRAVEL),
            Block::get(Block::GRAVEL),
            Block::get(Block::GRAVEL)
        ]);
    }

    public function getName(): string {
        return "Gravelly Mountains";
    }
}