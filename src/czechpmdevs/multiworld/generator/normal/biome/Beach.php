<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\generator\normal\biome;

use czechpmdevs\multiworld\generator\normal\populator\LakePopulator;
use pocketmine\level\biome\SandyBiome;

/**
 * Class Beach
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class Beach extends SandyBiome {

    public function __construct() {
        $this->setElevation(62, 65);
        $this->addPopulator(new LakePopulator());
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Beach";
    }
}