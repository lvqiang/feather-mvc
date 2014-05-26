<?php

namespace Feather\Util;

/**
 * UUID class
 *
 * The following class generates VALID RFC 4122 COMPLIANT
 * Universally Unique IDentifiers (UUID) version 3, 4 and 5.
 *
 * UUIDs generated validates using OSSP UUID Tool, and output
 * for named-based UUIDs are exactly the same. This is a pure
 * PHP implementation.
 *
 * @author Andrew Moore
 * @link http://www.php.net/manual/en/function.uniqid.php#94959
 */
class UUID {
    
    /**
     * Generate v3 UUID
     *
     * Version 3 UUIDs are named based. They require a namespace (another 
     * valid UUID) and a value (the name). Given the same namespace and 
     * name, the output is always the same.
     * 
     * @param string $namespace A uuid string
     * @param string $name
     * @return string A uuid of 36 length string  
     */
    static public function getV3UUID($namespace, $name) {
        if(!self::is_valid($namespace)) return false;
 
        // Get hexadecimal components of namespace
        $nhex = str_replace(array('-','{','}'), '', $namespace);
 
        // Binary Value
        $nstr = '';
 
        // Convert Namespace UUID to bits
        for($i = 0; $i < strlen($nhex); $i+=2) {
            $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
        }
 
        // Calculate hash value
        $hash = md5($nstr . $name);
 
        return sprintf('%08s-%04s-%04x-%04x-%12s',
 
        // 32 bits for "time_low"
        substr($hash, 0, 8),
 
        // 16 bits for "time_mid"
        substr($hash, 8, 4),
 
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 3
        (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
 
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
 
        // 48 bits for "node"
        substr($hash, 20, 12)
        );
    }
 
    /**
     * 
     * Generate v4 UUID
     * Version 4 UUIDs are pseudo-random.
     * 
     * @return string A uuid of 36 length
     */
    static public function getV4UUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
 
        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
 
        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),
 
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,
 
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,
 
        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
 
    /**
     * Generate v5 UUID
     * 
     * Version 5 UUIDs are named based. They require a namespace (another 
     * valid UUID) and a value (the name). Given the same namespace and 
     * name, the output is always the same.
     * 
     * @param string $namespace A uuid string
     * @param string $name
     * @return string A uuid of 36 string
     */
    static public function getV5UUID($namespace, $name) {
        if(!self::is_valid($namespace)) return false;
 
        // Get hexadecimal components of namespace
        $nhex = str_replace(array('-','{','}'), '', $namespace);
 
        // Binary Value
        $nstr = '';
 
        // Convert Namespace UUID to bits
        for($i = 0; $i < strlen($nhex); $i+=2) {
            $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
        }
 
        // Calculate hash value
        $hash = sha1($nstr . $name);
 
        return sprintf('%08s-%04s-%04x-%04x-%12s',
 
        // 32 bits for "time_low"
        substr($hash, 0, 8),
 
        // 16 bits for "time_mid"
        substr($hash, 8, 4),
 
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 5
        (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
 
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
 
        // 48 bits for "node"
        substr($hash, 20, 12)
        );
    }
 
    /**
    * Whether a valid uuid
    */
    static public function is_valid($uuid) {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
                      '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }

    /** 
    * Mongo Object ID
    * time - 4 byte(char)
    * machine id - 3 byte(char)
    * pid - 2 byte(char)
    * inc - 3 byte(char)
    *
    * @return string 24 length string
    */
    static public function getMongoUUID() {
        static $incBase = false;

        if ($incBase === false) {
            $incBase = mt_rand(0, 0xffffff);
        }

        $time = time();
        $machineId = mt_rand(0, 0xffffff);
        $processId = getmypid();
        $inc = ++$incBase;

        return sprintf(
            "%08x%06x%04x%06x",
            $time,
            $machineId,
            $processId,
            $inc
        );  
    }

    /**
    * customized uuid
    *
    * @return 33 length string
    */
    static public function getCustomizedUUID($namespace) {
        $prefix = hash('crc32', $namespace, false);
        return $prefix."-".self::getMongoUUID();
    }


}// END OF CLASS
