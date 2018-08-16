<?php

declare(strict_types=1);

namespace multiworld\api;

use pocketmine\level\format\io\BaseLevelProvider;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;


/**
 * Class WorldGameRulesAPI
 * @package multiworld\api
 */
class WorldGameRulesAPI {


    /**
     * @param Level $level
     *
     * @return array
     */
    public static function getLevelGameRules(Level $level): array {
        $levelProvider = $level->getProvider();
        if(!$levelProvider instanceof BaseLevelProvider) {
            return self::getDefaultGameRules();
        }

        $compound = $levelProvider->getLevelData()->getCompoundTag("GameRules");
        $gameRules = [];

        foreach (self::getAllGameRules() as $rule) {
            if($compound->offsetExists($rule)) {
                $value = self::getValueFromString($compound->getString($rule));
                $gameRules[$rule] = is_bool($value) ? [1, $value] : [2, $value];
            }
        }

        return $gameRules;
    }

    /**
     * @param Level $level
     * @param array $gameRules
     *
     * @return bool
     */
    public static function setLevelGameRules(Level $level, array $gameRules): bool {
        $levelProvider = $level->getProvider();
        if(!$levelProvider instanceof BaseLevelProvider) {
            return false;
        }

        $compound = $levelProvider->getLevelData()->getCompoundTag("GameRules");

        foreach ($gameRules as $index => [$type, $value]) {
            $compound->setString($index, self::getStringFromValue($value));
        }

        $levelProvider->saveLevelData();
        self::handleGameRuleChange($level, $gameRules);
        return true;
    }

    /**
     * @param Level $level
     * @param string $gameRule
     * @param bool $value
     *
     * @return bool
     */
    public static function updateLevelGameRule(Level $level, string $gameRule, bool $value) {
        $levelProvider = $level->getProvider();
        if(!$levelProvider instanceof BaseLevelProvider) {
            return false;
        }

        $compound = $levelProvider->getLevelData()->getCompoundTag("GameRules");
        $compound->setString($gameRule, self::getStringFromValue($value));

        $levelProvider->saveLevelData();
        self::reloadGameRules($level);
        self::handleGameRuleChange($level, [$gameRule => [1, $value]]);
        return true;
    }

    /**
     * @param Level $level
     */
    public static function reloadGameRules(Level $level) {
        foreach ($level->getPlayers() as $player) {
            self::updateGameRules($player);
        }
    }

    /**
     * @return array $gameRules
     */
    public static function getDefaultGameRules(): array {
        return [
            //"commandBlockOutput" => [1, true], not implemented
            "doDaylightCycle" => [1, true],
            //"doEntityDrops" => [1, true], useless
            //"doFireTick" => [1, true], not implemented
            //"doInsomnia" => [1, true], (1.6)
            "doMobLoot" => [1, true],
            //"doMobSpawning" => [1, true], not implemented
            "doTileDrops" => [1, true],
            //"doWeatherCycle" => [1, true], not implemented
            "keepInventory" => [1, false],
            //"maxCommandChainLength" => [2, 65536],
            //"mobGriefing" => [1, true], not implemented
            "naturalRegeneration" => [1, true],
            "pvp" => [1, true],
            //"sendCommandFeedback" => [1, true], not implemented
            "showcoordinates" => [1, false],
            "tntexplodes" => [1, true]
        ];
    }

    /**
     * @param string $lowerString
     *
     * @return string
     */
    public static function getRuleFromLowerString(string $lowerString) {
        $rules = [];
        foreach (self::getAllGameRules() as $rule) {
            $rules[strtolower($rule)] = $rule;
        }
        return isset($rules[$lowerString]) ? $rules[$lowerString] : $lowerString;
    }

    /**
     * @param Player $player
     */
    public static function updateGameRules(Player $player) {
        $pk = new GameRulesChangedPacket();
        $pk->gameRules = self::getLevelGameRules($player->getLevel());
        $player->dataPacket($pk);
    }

    /**
     * @return array
     */
    public static function getAllGameRules() {
        return array_keys(self::getDefaultGameRules());
    }

    /**
     * @param Level $level
     * @param array $gameRules
     */
    public static function handleGameRuleChange(Level $level, array $gameRules) {
        foreach ($gameRules as $gameRule => [$valueType, $value]) {
            switch ($gameRule) {
                case "doDaylightCycle":
                    $level->stopTime = !$value;
                    continue;
            }
        }
    }

    /**
     * @param bool|int|string $value
     *
     * @return string
     */
    private static function getStringFromValue($value): string {
        if(is_bool($value)) {
            return $value ? "true" : "false";
        }
        return (string) $value;
    }

    /**
     * @param string $string
     *
     * @return bool|int|string
     */
    private static function getValueFromString(string $string) {
        switch ($string) {
            case "true":
                return true;
            case "false":
                return false;
            default:
                if(is_numeric($string)) {
                    return (int) $string;
                }
                else {
                    return (string) $string;
                }
        }
    }
}