<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\generator\normal\biome;

use czechpmdevs\multiworld\generator\normal\populator\object\Plant;
use czechpmdevs\multiworld\generator\normal\populator\PlantPopulator;
use czechpmdevs\multiworld\generator\normal\populator\TallGrass;
use czechpmdevs\multiworld\generator\normal\populator\Tree;
use pocketmine\block\BrownMushroom;
use pocketmine\block\Dandelion;
use pocketmine\block\DoublePlant;
use pocketmine\block\Flower;
use pocketmine\block\RedMushroom;
use pocketmine\level\biome\GrassyBiome;

/**
 * Class TallBirchForest
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class TallBirchForest extends GrassyBiome {

    public function __construct() {
        parent::__construct();
        $this->setElevation(60, 80);

        $mushrooms = new PlantPopulator();
        $mushrooms->setBaseAmount(2);
        $mushrooms->setRandomAmount(2);
        $mushrooms->addPlant(new Plant(new BrownMushroom()));
        $mushrooms->addPlant(new Plant(new RedMushroom()));
        $mushrooms->setSpawnPercentage(95);

        $flowers = new PlantPopulator();
        $flowers->setBaseAmount(6);
        $flowers->setRandomAmount(7);
        $flowers->addPlant(new Plant(new Dandelion()));
        $flowers->addPlant(new Plant(new Flower()));
        $flowers->setSpawnPercentage(75);

        $roses = new PlantPopulator();
        $roses->setBaseAmount(5);
        $roses->setRandomAmount(4);
        $roses->addPlant(new Plant(new DoublePlant(4), new DoublePlant(12)));
        $roses->setSpawnPercentage(50);

        $peonys = new PlantPopulator();
        $peonys->setBaseAmount(5);
        $peonys->setRandomAmount(4);
        $peonys->addPlant(new Plant(new DoublePlant(1), new DoublePlant(9)));
        $peonys->setSpawnPercentage(50);

        $birch = new Tree(\czechpmdevs\multiworld\generator\normal\object\Tree::BIG_BIRCH);
        $birch->setBaseAmount(5);
        $birch->setRandomAmount(4);

        $this->addPopulator($birch);
        $this->addPopulator($flowers);
        $this->addPopulator($peonys);
        $this->addPopulator($roses);
        $this->addPopulator($mushrooms);

        $this->setElevation(66, 79);

        $tallGrass = new TallGrass();
        $tallGrass->setBaseAmount(56);
        $tallGrass->setRandomAmount(12);

        $this->addPopulator($tallGrass);

        $this->temperature = 0.8;
        $this->rainfall = 0.4;
    }

    public function getName(): string {
        return "Tall Birch Forest";
    }

}