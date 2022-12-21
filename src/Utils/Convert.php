<?php

namespace rohsyl\Salto\Utils;

class Convert
{
    /**
     * Convert an array of string to a binary string
     * @param array $strings
     * @return string
     */
    public static function stringArrayToBinaryString(array $strings) {
        $binaryString = '';
        foreach ($strings as $string) {
            // skip null field.
            if(!is_null($string)) {
                if(is_string($string)) {
                    $binaryString .= self::stringToBinary($string);
                }
                else {
                    $binaryString.= pack('C*', $string);
                }
            }
        }
        return $binaryString;
    }

    /**
     * Convert a string to a binary string
     * @param string $string
     * @return string
     */
    public static function stringToBinary(string $string) {
        $binary = '';
        foreach(str_split($string) as $char) {

            $binary .= pack('C*', ord($char));
        }
        return $binary;
    }

    /**
     * Convert a binary value into a decimal value.
     * @param $byte
     * @return int
     */
    public static function binaryToDecimal($byte) {
        return intval(join(unpack('C*', $byte)));
    }

    /**
     * Convert an array of decimal values into a hexadecimal string
     * @param $frame
     * @return string
     */
    public static function decimalToHexaString($frame) {
        $string = '';
        foreach($frame as $dec) {
            $string .= '0x' . str_pad(dechex($dec), 2, '0', STR_PAD_LEFT) . ' ';
        }
        return $string;
    }

    /**
     * Convert a binary string into a hexadecimal string
     * @param $frame
     * @return string
     */
    public static function binaryStringToHexaString($frame) {
        $decArray = unpack('C*', $frame);
        $string = '';
        foreach($decArray as $dec) {
            $string .= '0x' . str_pad(dechex($dec), 2, '0', STR_PAD_LEFT) . ' ';
        }
        return $string;
    }

    /**
     * Convert a decimal array into a string.
     * Each item in the array is the decimal representation of a char
     * @param array $array
     * @return string
     */
    public static function decimalArrayToString(array $array) {
        $string = '';
        foreach($array as $row) {
            $string .= chr($row);
        }
        return $string;
    }
}
