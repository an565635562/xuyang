<?php
/**
 * Auto generated from PB_base_data.proto at 2018-01-24 16:26:41
 */

namespace {
/**
 * EmailType enum
 */
final class EmailType
{
    const EmailType_None = 0;
    const EmailType_Sys = 1;
    const EmailType_Card = 2;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'EmailType_None' => self::EmailType_None,
            'EmailType_Sys' => self::EmailType_Sys,
            'EmailType_Card' => self::EmailType_Card,
        );
    }
}
}