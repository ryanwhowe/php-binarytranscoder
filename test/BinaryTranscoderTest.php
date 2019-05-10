<?php
/**
 * This file contains the definition for the BinaryTranscoderTest class
 *
 * @author Ryan Howe
 * @since  2018-09-29
 */
namespace Test;

use RyanWHowe\BinaryTranscoder\BinaryTranscoder;

class BinaryTranscoderTest extends \PHPUnit\Framework\TestCase
{

    /**
     * A dataProvider that has data in the form of $encodedValue, $expectedDecodedValue
     *
     * @return array
     */
    public function transcodingProvider()
    {
        return [
            [ bindec('1 000'), ['key1' => false, 'key2' => false, 'key3' => false] ],
            [ bindec('1 001'), ['key1' => true,  'key2' => false, 'key3' => false] ],
            [ bindec('1 010'), ['key1' => false, 'key2' => true,  'key3' => false] ],
            [ bindec('1 100'), ['key1' => false, 'key2' => false, 'key3' => true] ],
            [ bindec('1 011'), ['key1' => true,  'key2' => true,  'key3' => false] ],
            [ bindec('1 110'), ['key1' => false, 'key2' => true,  'key3' => true] ],
            [ bindec('1 111'), ['key1' => true,  'key2' => true,  'key3' => true] ],
        ];
    }

    /**
     * A dataProvider that has data in the form of $encodedStoredValue, $expectedDecodedValue .. These
     * expectedDecodedValues and what are expected when the Padding_Boolean is set to false
     *
     * @return array
     */
    public function forwardTranscodingFalsePadProvider()
    {
        return [
            [ bindec('1 000'), ['key1' => false, 'key2' => false, 'key3' => false, 'key4' => false] ],
            [ bindec('1 001'), ['key1' => true,  'key2' => false, 'key3' => false, 'key4' => false] ],
            [ bindec('1 010'), ['key1' => false, 'key2' => true,  'key3' => false, 'key4' => false] ],
            [ bindec('1 100'), ['key1' => false, 'key2' => false, 'key3' => true,  'key4' => false] ],
            [ bindec('1 011'), ['key1' => true,  'key2' => true,  'key3' => false, 'key4' => false] ],
            [ bindec('1 110'), ['key1' => false, 'key2' => true,  'key3' => true,  'key4' => false] ],
            [ bindec('1 111'), ['key1' => true,  'key2' => true,  'key3' => true,  'key4' => false] ],
            [
                bindec('1111'),
                ['key1' => true,  'key2' => true,  'key3' => true,  'key4' => false, 'key5' => false, 'key6' => false]
            ],
        ];
    }

    /**
     * A dataProvider that has data in the form of $encodedStoredValue, $expectedDecodedValue .. These
     * expectedDecodedValues and what are expected when the Padding_Boolean is set to true
     *
     * @return array
     */
    public function forwardTranscodingTruePadProvider()
    {
        return [
            [ bindec('1 000'), ['key1' => false, 'key2' => false, 'key3' => false, 'key4' => true] ],
            [ bindec('1 001'), ['key1' => true,  'key2' => false, 'key3' => false, 'key4' => true] ],
            [ bindec('1 010'), ['key1' => false, 'key2' => true,  'key3' => false, 'key4' => true] ],
            [ bindec('1 100'), ['key1' => false, 'key2' => false, 'key3' => true,  'key4' => true] ],
            [ bindec('1 011'), ['key1' => true,  'key2' => true,  'key3' => false, 'key4' => true] ],
            [ bindec('1 110'), ['key1' => false, 'key2' => true,  'key3' => true,  'key4' => true] ],
            [ bindec('1 111'), ['key1' => true,  'key2' => true,  'key3' => true,  'key4' => true] ],
            [
                bindec('1111'),
                ['key1' => true,  'key2' => true,  'key3' => true,  'key4' => true, 'key5' => true, 'key6' => true]
            ],
        ];
    }

    /**
     * A dataProvider that has data in the form of $encodedStoredValue, $expectedDecodedValue .. These
     * expectedDecodedValues and what are expected when the Padding_Boolean is set to null
     *
     * @return array
     */
    public function forwardTranscodingNullPadProvider()
    {
        return [
            [ bindec('1 000'), ['key1' => false, 'key2' => false, 'key3' => false, 'key4' => null] ],
            [ bindec('1 001'), ['key1' => true,  'key2' => false, 'key3' => false, 'key4' => null] ],
            [ bindec('1 010'), ['key1' => false, 'key2' => true,  'key3' => false, 'key4' => null] ],
            [ bindec('1 100'), ['key1' => false, 'key2' => false, 'key3' => true,  'key4' => null] ],
            [ bindec('1 011'), ['key1' => true,  'key2' => true,  'key3' => false, 'key4' => null] ],
            [ bindec('1 110'), ['key1' => false, 'key2' => true,  'key3' => true,  'key4' => null] ],
            [ bindec('1 111'), ['key1' => true,  'key2' => true,  'key3' => true,  'key4' => null] ],
            [
                bindec('1111'),
                ['key1' => true,  'key2' => true,  'key3' => true,  'key4' => null, 'key5' => null, 'key6' => null]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider transcodingProvider
     * @dataProvider forwardTranscodingFalsePadProvider
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::__construct()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::decodeInteger()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::convertStringToArray()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::determineMaxArrayLength()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::convertProtectedIntToBin()
     * @param $encoded_int
     * @param $expected_decode
     * @throws \Exception
     */
    public function decodingFalsePad($encoded_int, $expected_decode)
    {
        $key_array = $expected_decode;
        array_walk(
            $key_array,
            function (&$item) {
                $item = null;
            }
        );
        $tr = new BinaryTranscoder($expected_decode);
        $decode = $tr->decodeInteger($encoded_int);

        $this->assertEquals($expected_decode, $decode);
    }

    /**
     * @test
     * @dataProvider transcodingProvider
     * @dataProvider forwardTranscodingTruePadProvider
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::decodeInteger()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::__construct()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::convertProtectedIntToBin()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::convertStringToArray()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::determineMaxArrayLength()
     * @param $encoded_int
     * @param $expected_decode
     * @throws \Exception
     */
    public function decodingTruePad($encoded_int, $expected_decode)
    {

        $key_array = $expected_decode;
        array_walk(
            $key_array,
            function (&$item) {
                $item = null;
            }
        );
        $tr = new BinaryTranscoder($key_array, PHP_INT_MAX, BinaryTranscoder::BINARYTRANSCODER_PAD_TRUE);
        $decode = $tr->decodeInteger($encoded_int);

        $this->assertEquals($expected_decode, $decode);
    }

    /**
     * @test
     * @dataProvider transcodingProvider
     * @dataProvider forwardTranscodingNullPadProvider
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::decodeInteger()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::__construct()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::convertProtectedIntToBin()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::convertStringToArray()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::determineMaxArrayLength()
     * @param $encoded_int
     * @param $expected_decode
     * @throws \Exception
     */
    public function decodingNullPad($encoded_int, $expected_decode)
    {

        $key_array = $expected_decode;
        array_walk(
            $key_array,
            function (&$item) {
                $item = null;
            }
        );
        $tr = new BinaryTranscoder($key_array, PHP_INT_MAX, BinaryTranscoder::BINARYTRANSCODER_PAD_NULL);
        $decode = $tr->decodeInteger($encoded_int);

        $this->assertEquals($expected_decode, $decode);
    }

    /**
     * @test
     * @dataProvider transcodingProvider
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::encodeArray()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::__construct()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::convertArrayToString()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::convertBinToProtectedInt()
     * @covers       \RyanWHowe\BinaryTranscoder\BinaryTranscoder::determineMaxArrayLength()
     * @param $expected_encode_int
     * @param $test_array
     * @throws \Exception
     */
    public function encoding($expected_encode_int, $test_array)
    {

        $tr = new BinaryTranscoder($test_array);
        $encoded = $tr->encodeArray($test_array);

        $this->assertEquals($expected_encode_int, $encoded);
    }

    /**
     * @test
     * @expectedException \RyanWHowe\BinaryTranscoder\BinaryTranscoderMaxLengthException
     * @covers \RyanWHowe\BinaryTranscoder\BinaryTranscoder::__construct()
     * @covers \RyanWHowe\BinaryTranscoder\BinaryTranscoder::determineMaxArrayLength
     * @throws \Exception
     */
    public function maxItemCountOverFlow()
    {
        $array_count = strlen(decbin(PHP_INT_MAX));
        $test_array = array();
        for ($i = 1; $i <= $array_count; $i++) {
            $test_array[$i] = false;
        }
        new BinaryTranscoder($test_array);
    }

    /**
     * @test
     * @dataProvider             transcodingProvider
     * @covers                   \RyanWHowe\BinaryTranscoder\BinaryTranscoder::encodeArray()
     * @covers                   \RyanWHowe\BinaryTranscoder\BinaryTranscoder::__construct
     * @covers                   \RyanWHowe\BinaryTranscoder\BinaryTranscoder::convertArrayToString
     * @covers                   \RyanWHowe\BinaryTranscoder\BinaryTranscoder::determineMaxArrayLength
     * @param $expected_encode_int
     * @param $test_array
     * @expectedException \RyanWHowe\BinaryTranscoder\BinaryTranscoderException
     * @expectedExceptionMessage The source array has a different length than the output string!
     * @throws \Exception
     */
    public function itemCountMissMatchEncoder($expected_encode_int, $test_array)
    {
        $tr = new BinaryTranscoder($test_array);
        array_push($test_array, array('key_z' => false));
        $tr->encodeArray($test_array);
    }

    /**
     * @test
     * @dataProvider             transcodingProvider
     * @dataProvider             forwardTranscodingFalsePadProvider
     * @expectedException \RyanWHowe\BinaryTranscoder\BinaryTranscoderException
     * @expectedExceptionMessage The key array does not have enough elements for the decoded integer
     * @covers                   \RyanWHowe\BinaryTranscoder\BinaryTranscoder::decodeInteger()
     * @covers                   \RyanWHowe\BinaryTranscoder\BinaryTranscoder::__construct
     * @covers                   \RyanWHowe\BinaryTranscoder\BinaryTranscoder::convertProtectedIntToBin
     * @covers                   \RyanWHowe\BinaryTranscoder\BinaryTranscoder::convertStringToArray
     * @covers                   \RyanWHowe\BinaryTranscoder\BinaryTranscoder::determineMaxArrayLength
     * @param $encoded_int
     * @param $expected_decode
     * @throws \Exception
     */
    public function itemCountMissMatchDecoder($encoded_int, $expected_decode)
    {
        $key_array = array_keys($expected_decode);
        $tr = new BinaryTranscoder($key_array);
        $failure_decode = bindec(str_pad('1', count($key_array) + 2, '0', STR_PAD_RIGHT));
        $decode = $tr->decodeInteger($failure_decode);
        $this->assertEquals($expected_decode, $decode);
    }
}
