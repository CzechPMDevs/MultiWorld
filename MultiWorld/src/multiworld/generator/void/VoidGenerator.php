<?php

declare(strict_types=1);

namespace multiworld\generator\void;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

/**
 * Class VoidGenerator
 * @package multiworld\generator\void
 */
class VoidGenerator extends Generator {

    /** @var ChunkManager $level */
    protected $level;

    /** @var Random $random */
    protected $random;

    /** @var array $options */
    private $options;


    /**
     * @return array
     */
    public function getSettings() : array {
        return [];
    }

    /**
     * @return string
     */
    public function getName() : string {
        return "void";
    }

    /**
     * VoidGenerator constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings = []){
        $this->options = $settings;
    }

    /**
     * @param ChunkManager $level
     * @param Random       $random
     *
     * @return mixed|void
     */
    public function init(ChunkManager $level, Random $random) : void{
        $this->level = $level;
        $this->random = $random;
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function generateChunk(int $chunkX, int $chunkZ) : void{
        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        for($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                for($y = 0; $y < 168; ++$y) {
                    $spawn = $this->getSpawn();
                    if($spawn->getX() >> 4 === $chunkX && $spawn->getZ() >> 4 === $chunkZ){
                        $chunk->setBlockId(0, 64, 0, Block::GRASS);
                    }
                    else {
                        $chunk->setBlockId($x, $y, $z, Block::AIR);
                    }
                }
            }
        }
        $chunk->setGenerated(true);
    }

    /**
     * @param $chunkX
     * @param $chunkZ
     *
     * @return mixed|void
     */
    public function populateChunk(int $chunkX, int $chunkZ) : void{}

    /**
     * @return Vector3
     */
    public function getSpawn(): Vector3 {
        return new Vector3(256, 65, 256);
    }
}
