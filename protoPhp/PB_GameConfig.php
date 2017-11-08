<?php
/**
 * Auto generated from PB_game_config.proto at 2017-11-07 17:09:46
 */

namespace {
/**
 * PB_GameConfig message
 */
class PB_GameConfig extends \ProtobufMessage
{
    /* Field index constants */
    const GAMEID = 1;
    const LEVEL = 2;
    const SERVERID = 3;
    const PLAYER_MAX = 4;
    const ROOM_MAX = 5;
    const MIN_GUN_COIN = 6;
    const MAX_GUN_COIN = 7;
    const MIN_GUN_LV = 8;
    const MAX_GUN_LV = 9;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::GAMEID => array(
            'name' => 'gameid',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::LEVEL => array(
            'name' => 'level',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::SERVERID => array(
            'name' => 'serverid',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::PLAYER_MAX => array(
            'name' => 'player_max',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::ROOM_MAX => array(
            'name' => 'room_max',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::MIN_GUN_COIN => array(
            'name' => 'min_gun_coin',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::MAX_GUN_COIN => array(
            'name' => 'max_gun_coin',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::MIN_GUN_LV => array(
            'name' => 'min_gun_lv',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::MAX_GUN_LV => array(
            'name' => 'max_gun_lv',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::GAMEID] = null;
        $this->values[self::LEVEL] = null;
        $this->values[self::SERVERID] = null;
        $this->values[self::PLAYER_MAX] = null;
        $this->values[self::ROOM_MAX] = null;
        $this->values[self::MIN_GUN_COIN] = null;
        $this->values[self::MAX_GUN_COIN] = null;
        $this->values[self::MIN_GUN_LV] = null;
        $this->values[self::MAX_GUN_LV] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'gameid' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setGameid($value)
    {
        return $this->set(self::GAMEID, $value);
    }

    /**
     * Returns value of 'gameid' property
     *
     * @return integer
     */
    public function getGameid()
    {
        $value = $this->get(self::GAMEID);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'level' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setLevel($value)
    {
        return $this->set(self::LEVEL, $value);
    }

    /**
     * Returns value of 'level' property
     *
     * @return integer
     */
    public function getLevel()
    {
        $value = $this->get(self::LEVEL);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'serverid' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setServerid($value)
    {
        return $this->set(self::SERVERID, $value);
    }

    /**
     * Returns value of 'serverid' property
     *
     * @return integer
     */
    public function getServerid()
    {
        $value = $this->get(self::SERVERID);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'player_max' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setPlayerMax($value)
    {
        return $this->set(self::PLAYER_MAX, $value);
    }

    /**
     * Returns value of 'player_max' property
     *
     * @return integer
     */
    public function getPlayerMax()
    {
        $value = $this->get(self::PLAYER_MAX);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'room_max' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setRoomMax($value)
    {
        return $this->set(self::ROOM_MAX, $value);
    }

    /**
     * Returns value of 'room_max' property
     *
     * @return integer
     */
    public function getRoomMax()
    {
        $value = $this->get(self::ROOM_MAX);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'min_gun_coin' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setMinGunCoin($value)
    {
        return $this->set(self::MIN_GUN_COIN, $value);
    }

    /**
     * Returns value of 'min_gun_coin' property
     *
     * @return integer
     */
    public function getMinGunCoin()
    {
        $value = $this->get(self::MIN_GUN_COIN);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'max_gun_coin' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setMaxGunCoin($value)
    {
        return $this->set(self::MAX_GUN_COIN, $value);
    }

    /**
     * Returns value of 'max_gun_coin' property
     *
     * @return integer
     */
    public function getMaxGunCoin()
    {
        $value = $this->get(self::MAX_GUN_COIN);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'min_gun_lv' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setMinGunLv($value)
    {
        return $this->set(self::MIN_GUN_LV, $value);
    }

    /**
     * Returns value of 'min_gun_lv' property
     *
     * @return integer
     */
    public function getMinGunLv()
    {
        $value = $this->get(self::MIN_GUN_LV);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'max_gun_lv' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setMaxGunLv($value)
    {
        return $this->set(self::MAX_GUN_LV, $value);
    }

    /**
     * Returns value of 'max_gun_lv' property
     *
     * @return integer
     */
    public function getMaxGunLv()
    {
        $value = $this->get(self::MAX_GUN_LV);
        return $value === null ? (integer)$value : $value;
    }
}
}