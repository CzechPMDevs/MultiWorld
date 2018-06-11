<?php

declare(strict_types=1);

namespace multiworld\generator\skyblock;

use buildertools\commands\TreeCommand;
use multiworld\generator\skyblock\populator\Island;
use pocketmine\block\Sapling;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\populator\Tree;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class SkyBlockGenerator extends Generator {

    /** @var ChunkManager $level */
    protected $level;

    /** @var Random $random */
    protected $random;

    /** @var array $options */
    private $options;

    public function __construct(array $settings = []) {
        $this->options = $settings;
    }

    public function init(ChunkManager $level, Random $random) : void{
        $this->level = $level;
        $this->random = $random;
    }

    public function generateChunk(int $chunkX, int $chunkZ) : void{
        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        for($x = 0; $x < 16; ++$x) {
            for($z = 0; $z < 16; ++$z) {
                for($y = 0; $y < 168; ++$y) {
                    $chunk->setBlockId($x, $y, $z, 0);
                }
            }
        }
    }

    public function populateChunk(int $chunkX, int $chunkZ) : void{
        if($chunkX === 16 && $chunkZ === 16) {
            $island = new Island;
            $island->populate($this->level, $chunkX, $chunkZ, $this->random);
        }
    }

    public function getName(): string {
        return "skyblock";
    }

    public function getSettings(): array {
        return [];
    }

    public function getSpawn(): Vector3 {
        return new Vector3(256, 70, 256);
    }

}
