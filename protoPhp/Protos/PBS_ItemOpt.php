<?php
/**
 * Auto generated from PB_usr_rpc.proto at 2017-08-17 23:26:56
 *
 * protos package
 */

namespace Protos {
/**
 * PBS_ItemOpt message
 */
class PBS_ItemOpt extends \ProtobufMessage
{
    /* Field index constants */
    const ITEMID = 1;
    const NUM = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::ITEMID => array(
            'name' => 'itemid',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::NUM => array(
            'name' => 'num',
            'required' => true,
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
        $this->values[self::ITEMID] = null;
        $this->values[self::NUM] = null;
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
     * Sets value of 'itemid' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setItemid($value)
    {
        return $this->set(self::ITEMID, $value);
    }

    /**
     * Returns value of 'itemid' property
     *
     * @return integer
     */
    public function getItemid()
    {
        $value = $this->get(self::ITEMID);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'num' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setNum($value)
    {
        return $this->set(self::NUM, $value);
    }

    /**
     * Returns value of 'num' property
     *
     * @return integer
     */
    public function getNum()
    {
        $value = $this->get(self::NUM);
        return $value === null ? (integer)$value : $value;
    }
}
}