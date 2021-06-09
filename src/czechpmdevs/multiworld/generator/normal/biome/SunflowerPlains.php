<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\generator\normal\biome;

use czechpmdevs\multiworld\generator\normal\populator\PlantPopulator;

/**
 * Class SunflowerPlains
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class SunflowerPlains extends Plains {

    /**
     * SunflowerPlains constructor.
     */
    public function __construct() {
        parent::__construct();

        $sunflowers = new PlantPopulator();
        $sunflowers->setBaseAmount(12);
        $sunflowers->setRandomAmount(6);
        $sunflowers->setSpawnPercentage(98);

        $this->addPopulator($sunflowers);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "SunflowerPlains";
    }
}