<?php
/**
 * Auto generated from PB_usr_rpc.proto at 2017-09-07 01:23:17
 *
 * protos package
 */

namespace Protos {
/**
 * OptSrc enum
 */
final class OptSrc
{
    const Src_PHP = 1;
    const Src_Game = 2;
    const Src_Hall = 3;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'Src_PHP' => self::Src_PHP,
            'Src_Game' => self::Src_Game,
            'Src_Hall' => self::Src_Hall,
        );
    }
}
}