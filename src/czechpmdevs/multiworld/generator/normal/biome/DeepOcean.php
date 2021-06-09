<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\generator\normal\biome;

class DeepOcean extends Ocean {

    public function __construct() {
        parent::__construct();
        $this->setElevation(40, 56);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Deep Ocean";
    }
}