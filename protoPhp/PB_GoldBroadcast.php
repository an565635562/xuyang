<?php
/**
 * Auto generated from PB_base_data.proto at 2018-01-24 16:26:41
 */

namespace {
/**
 * PB_GoldBroadcast message
 */
class PB_GoldBroadcast extends \ProtobufMessage
{
    /* Field index constants */
    const VIP = 1;
    const PLAYER_NAME = 2;
    const FISH_TYPE = 3;
    const GOLD = 4;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::VIP => array(
            'name' => 'vip',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::PLAYER_NAME => array(
            'name' => 'player_name',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::FISH_TYPE => array(
            'name' => 'fish_type',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::GOLD => array(
            'name' => 'gold',
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
        $this->values[self::VIP] = null;
        $this->values[self::PLAYER_NAME] = null;
        $this->values[self::FISH_TYPE] = null;
        $this->values[self::GOLD] = null;
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
     * Sets value of 'vip' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setVip($value)
    {
        return $this->set(self::VIP, $value);
    }

    /**
     * Returns value of 'vip' property
     *
     * @return integer
     */
    public function getVip()
    {
        $value = $this->get(self::VIP);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'player_name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setPlayerName($value)
    {
        return $this->set(self::PLAYER_NAME, $value);
    }

    /**
     * Returns value of 'player_name' property
     *
     * @return string
     */
    public function getPlayerName()
    {
        $value = $this->get(self::PLAYER_NAME);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'fish_type' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setFishType($value)
    {
        return $this->set(self::FISH_TYPE, $value);
    }

    /**
     * Returns value of 'fish_type' property
     *
     * @return integer
     */
    public function getFishType()
    {
        $value = $this->get(self::FISH_TYPE);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'gold' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setGold($value)
    {
        return $this->set(self::GOLD, $value);
    }

    /**
     * Returns value of 'gold' property
     *
     * @return integer
     */
    public function getGold()
    {
        $value = $this->get(self::GOLD);
        return $value === null ? (integer)$value : $value;
    }
}
}