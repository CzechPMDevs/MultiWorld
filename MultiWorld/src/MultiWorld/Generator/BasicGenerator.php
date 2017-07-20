<?php

namespace MultiWorld\Generator;

use MultiWorld\MultiWorld;
use pocketmine\level\generator\Generator;
use pocketmine\Server;

class BasicGenerator {

    /** @var  MultiWorld */
    public $plugin;

    const NORMAL = 0;
    const FLAT = 1;
    const NETHER = 2;
    const VOID = 3;

    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param string $name
     * @param int|string $seed
     * @param string $generatorName
     */
    public function generateLevel(string $name, int $seed = 0, string $generatorName) {
        $seed = intval($seed);
        // Flat is advanced -> if($generatorName == "FlatGenerator") $generator = "...");
        $generator = $this->getBasicGeneratorByName($generatorName);
        // random seed
        if($seed == 0) $seed = rand(0, 9999);
        if(!is_dir(MultiWorld::getInstance()->configmgr->getDataPath()."world/{$name}")) {
            Server::getInstance()->generateLevel($name, $seed, $generator);
            Server::getInstance()->broadcastMessage(MultiWorld::getPrefix()."Level {$name} is generated using seed: {$seed} & generator: {$generatorName}.");
        }


    }

    /**
     * @param string $generatorName
     * @return Generator $generator
     */
    public function getBasicGeneratorByName($generatorName) {
        switch (strtolower($generatorName)) {
            case "normal":
            case "default":
            case "classic":
            case "world":
            case self::NORMAL:
                if(MultiWorld::getInstance()->getServer()->getName()=="PocketMine-MP") {
                    $generator = Generator::getGenerator("normal");
                    return $generator;
                }
                else {
                    $generator = Generator::getGenerator("normal");
                    return $generator;
                }
                break;
            case "flat":
            case "superflat":
            case self::FLAT:
                if(MultiWorld::getInstance()->getServer()->getName()=="PocketMine-MP") {
                    $generator = Generator::getGenerator("flat");
                    return $generator;
                }
                else {
                    $generator = Generator::getGenerator("flat");
                    return $generator;
                }
                break;
            case "nether":
            case "hell":
            case self::NETHER:
                if(MultiWorld::getInstance()->getServer()->getName()=="PocketMine-MP") {
                    $generator = Generator::getGenerator("hell");
                    return $generator;
                }
                break;
            case self::VOID:
            case "void":
                // will be added
                if(MultiWorld::getInstance()->getServer()->getName()=="PocketMine-MP") {
                    MultiWorld::getInstance()->getLogger()->critical("§4Void generator not found!");
                    $generator = Generator::getGenerator("flat");
                    return $generator;
                }
                else {
                    $generator = Generator::getGenerator("void");
                    return $generator;
                }
        }
    }
}
