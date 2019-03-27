<?php
/**
 * This file contains the code for the BinaryTrascoder Class
 *
 * @since 2018-09-28
 * @author Ryan Howe
 * @version 1.0.0
 */

namespace ryanwhowe;

/**
 * Class BinaryTranscoder
 *
 * This class is for encoding and decoding integer values into a binary sequence for representing an array
 * of boolean values.  There are certain steps that need to be taken to ensure that backward compatibility
 * will be maintained as new values are added to the array of booleans it will simply default the values to
 * the pad_boolean value (default to false) for anything that is newly added to the bottom of the field array
 *
 * @author Ryan Howe
 * @since 2018-09-29
 * @package ryanwhowe/php-binarytranscoder
 */
class BinaryTranscoder
{

    /**
     * Pad the unmapped key values to false
     */
    const BINARYTRANSCODER_PAD_FALSE = false;

    /**
     * Pad the unmapped key values to true
     */
    const BINARYTRANSCODER_PAD_TRUE = true;

    /**
     * Pad the unmapped key values to null
     */
    const BINARYTRANSCODER_PAD_NULL = null;

    /**
     * @var array The array of ordered keys that will be associated with the binary output
     */
    private $key_array;

    /**
     * @var integer the number of elements in the key array, used to determine the total binary string length used
     */
    private $array_length;

    /**
     * @var boolean Determines if the backwards compatibility is to pad new entries defaulted to true or to false,
     * default to false
     */
    private $pad_boolean;

    /**
     * BinaryTranscoder constructor.
     *
     * @param array $key_array The array of ordered keys that will be associated with the binary output
     * @param boolean $pad_boolean  The default backwards compatibility value when new array elements are added
     * @throws BinaryTranscoderMaxLengthException if more array elements than what can be transcoded are passes to the constructor
     */
    public function __construct(array $key_array, $pad_boolean = self::BINARYTRANSCODER_PAD_FALSE)
    {
        $this->key_array = $this->getKeyArray($key_array);

        $count = count($this->key_array);
        $max_count = self::determineMaxArrayLength();
        if ($count > $max_count) {
            throw new BinaryTranscoderMaxLengthException("The maximum amount of array field that can be transcoded is 
            {$max_count}");
        }
        $this->array_length = $count;
        if (\null === $pad_boolean) {
            $this->pad_boolean = \null;
        } else {
            $this->pad_boolean = (boolean)$pad_boolean;
        }
    }

    private function getKeyArray(array $array)
    {
        if ($this->hasStringKeys($array)) {
            return array_keys($array);
        } else {
            return $array;
        }
    }

    private function hasStringKeys(array $array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    /**
     * Return the array representation of the decoded protected integer value
     *
     * @param $integer
     * @return array
     * @throws \Exception when the decoded integer does not match the array length generated
     */
    public function decodeInteger($integer)
    {
        $decoded = $this->convertProtectedIntToBin($integer);
        $decoded_array = $this->convertStringToArray($decoded);
        return $decoded_array;
    }

    /**
     * Return the protected integer representation of the passed array
     *
     * @return integer
     * @param array $array_values
     * @throws \Exception when the passed array of values is of different length than the instantiated key array
     */
    public function encodeArray(array $array_values)
    {
        $result = $this->convertBinToProtectedInt($this->convertArrayToString($array_values));
        return $result;
    }

    /**
     * This function will convert a protected int into a binary representation.  The protected integers
     * are encoded with a leading 1 to preserver the true length of the binary string.  This converter will
     * back fill the binary string with 0 values to the current length of the key array for the class as it was
     * instantiated.
     *
     * @param $source_int integer
     * @return string
     */
    private function convertProtectedIntToBin($source_int)
    {
        $result = decbin($source_int);
        $result = substr($result, 1, strlen($result));
        if (\null === $this->pad_boolean) {
            return $result;
        } else {
            $pad_string = ($this->pad_boolean) ? '1' : '0';
            return str_pad($result, $this->array_length, $pad_string, STR_PAD_RIGHT);
        }
    }

    /**
     * This function will convert the binary to a protected int.  The protected Int has a leading 1 to
     * preserve the full original length of the array and all the values withing it discretely.  Without
     * the leading 0 the value of 000010 would be stored as 2 and decode to 10.  This protection
     * is necessary for the backwards compatibility of this class.  Without it we would not be able
     * to append new values to the end of the key array and have everything work correctly.  This
     * effectively creates a minimum value that can be stored for a given number of array elements.
     *
     * @param $source_string string the source binary string
     * @return number
     */
    private function convertBinToProtectedInt($source_string)
    {
        $pad_string = ($this->pad_boolean) ? '1' : '0';
        $protected_binary = '1' . str_pad($source_string, $this->array_length, $pad_string, STR_PAD_RIGHT);
        $result = bindec($protected_binary);
        return $result;
    }

    /**
     * Convert the internal stored field array into a binary string representation
     *
     * @param $array_values array The array of values to be converted to a binary string
     * @return string binary representation of the passed boolean array
     * @throws \Exception when the passed array of values is of different length than the instantiated key array
     */
    private function convertArrayToString($array_values)
    {
        array_walk(
            $array_values,
            function (&$item) {
                $item = ($item) ? '1' : '0';
            }
        );
        $result =  \implode($array_values);
        if ($this->array_length != strlen($result)) {
            throw new BinaryTranscoderException('The source array has a different length than the output string!');
        }
        return $result;
    }

    /**
     * Convert a binary string to an array using the internal field array as the new key values
     *
     * @param $source_string string The binary source string to convert
     * @return array
     * @throws \Exception when the string length does not match the array length
     */
    private function convertStringToArray($source_string)
    {
        $key_array = $this->key_array;
        $value_array = str_split($source_string);
        array_walk(
            $value_array,
            function (&$item) {
                $item = (bool)$item;
            }
        );
        if (count($key_array) < count($value_array)) {
            throw new BinaryTranscoderException(
                'The key array does not have enough elements for the decoded integer'
            );
        }
        //WD:RWH - 2018-10-09: this is where the trimming of the value array will be needed to match the key array
        if (null === $this->pad_boolean) {
            $value_array = \array_merge(
                $value_array,
                array_fill(count($value_array), count($key_array)-count($value_array), \null)
            );
        }
        if (count($key_array) !== count($value_array)) {
            throw new BinaryTranscoderException(
                'The passed string does not have enough elements for the key array'
            );
        }
        $result = array_combine($key_array, $value_array);
        return $result;
    }

    /**
     * Determine the maximum amount of binary switches that can be stored with the passed
     * integer value.  The default value is PHP_INT_MAX, but if you are using a database to
     * store this information you need to use the maximum value that can be stored in the
     * column that the data is being stored in.
     *
     * @param int $int_max
     * @return int
     */
    public static function determineMaxArrayLength($int_max = PHP_INT_MAX)
    {
        return strlen(decbin($int_max)) - 1;
    }
}
