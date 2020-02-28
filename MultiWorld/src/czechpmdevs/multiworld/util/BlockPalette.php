<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\util;

/**
 * Class BlockPalette
 * @package czechpmdevs\multiworld\util
 */
class BlockPalette {

    /** @var SimpleBlockData[] $palette */
    public $palette = [];

    /**
     * @param SimpleBlockData $block
     */
    public function registerBlock(SimpleBlockData $block) {
        $this->palette[] = $block;
    }

    /**
     * @param int $id
     * @return SimpleBlockData
     */
    public function getBlock(int $id) {
        return $this->palette[$id] ?? new SimpleBlockData(0, 0);
    }
}