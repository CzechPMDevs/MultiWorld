<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\generator\normal\populator\object;

use pocketmine\block\Block;

/**
 * Class Plant
 * @package czechpmdevs\multiworld\generator\normal\populator\object
 */
class Plant {

    /** @var Block[] $blocks */
    public $blocks = [];

    /**
     * Plant constructor.
     * @param Block $baseBlock
     * @param Block|null $secondaryBlock
     */
    public function __construct(Block $baseBlock, Block $secondaryBlock = null) {
        $this->blocks = [$baseBlock];
        if($secondaryBlock !== null) {
            $this->blocks[] = $secondaryBlock;
        }
    }
}