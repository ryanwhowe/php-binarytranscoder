<?php
/**
 * This file contains the definition for the BinaryTranscoderTest class
 *
 * @author Ryan Howe
 * @since  2018-09-29
 */

class BinaryTranscoderTest extends \PHPUnit\Framework\TestCase {

    /**
     * A dataProvider that has data in the form of $encodedValue, $expectedDecodedValue
     *
     * @return array
     */
    public function transcodingProvider()
    {
        return [
            [ bindec('1000'), ['key1' => false, 'key2' => false, 'key3' => false] ],
            [ bindec('1100'), ['key1' => true,  'key2' => false, 'key3' => false] ],
            [ bindec('1010'), ['key1' => false, 'key2' => true,  'key3' => false] ],
            [ bindec('1001'), ['key1' => false, 'key2' => false, 'key3' => true] ],
            [ bindec('1110'), ['key1' => true,  'key2' => true,  'key3' => false] ],
            [ bindec('1011'), ['key1' => false, 'key2' => true,  'key3' => true] ],
            [ bindec('1111'), ['key1' => true,  'key2' => true,  'key3' => true] ],
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
            [ bindec('1000'), ['key1' => false, 'key2' => false, 'key3' => false, 'key4' => false] ],
            [ bindec('1100'), ['key1' => true,  'key2' => false, 'key3' => false, 'key4' => false] ],
            [ bindec('1010'), ['key1' => false, 'key2' => true,  'key3' => false, 'key4' => false] ],
            [ bindec('1001'), ['key1' => false, 'key2' => false, 'key3' => true,  'key4' => false] ],
            [ bindec('1110'), ['key1' => true,  'key2' => true,  'key3' => false, 'key4' => false] ],
            [ bindec('1011'), ['key1' => false, 'key2' => true,  'key3' => true,  'key4' => false] ],
            [ bindec('1111'), ['key1' => true,  'key2' => true,  'key3' => true,  'key4' => false] ],
            [ bindec('1111'), ['key1' => true,  'key2' => true,  'key3' => true,  'key4' => false, 'key5' => false, 'key6' => false] ],
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
            [ bindec('1000'), ['key1' => false, 'key2' => false, 'key3' => false, 'key4' => true] ],
            [ bindec('1100'), ['key1' => true,  'key2' => false, 'key3' => false, 'key4' => true] ],
            [ bindec('1010'), ['key1' => false, 'key2' => true,  'key3' => false, 'key4' => true] ],
            [ bindec('1001'), ['key1' => false, 'key2' => false, 'key3' => true,  'key4' => true] ],
            [ bindec('1110'), ['key1' => true,  'key2' => true,  'key3' => false, 'key4' => true] ],
            [ bindec('1011'), ['key1' => false, 'key2' => true,  'key3' => true,  'key4' => true] ],
            [ bindec('1111'), ['key1' => true,  'key2' => true,  'key3' => true,  'key4' => true] ],
            [ bindec('1111'), ['key1' => true,  'key2' => true,  'key3' => true,  'key4' => true, 'key5' => true, 'key6' => true] ],
        ];
    }

    /**
     * @test
     * @dataProvider transcodingProvider
     * @dataProvider forwardTranscodingFalsePadProvider
     * @covers       \ryanwhowe\BinaryTranscoder::__construct()
     * @covers       \ryanwhowe\BinaryTranscoder::decodeInteger()
     * @covers       \ryanwhowe\BinaryTranscoder::convertStringToArray()
     * @covers       \ryanwhowe\BinaryTranscoder::determineMaxArrayLength()
     * @covers       \ryanwhowe\BinaryTranscoder::convertProtectedIntToBin()
     * @param $encoded_int
     * @param $expected_decode
     * @throws Exception
     */
    public function decodingFalsePad($encoded_int, $expected_decode)
    {

        $key_array = array_keys($expected_decode);
        $tr = new ryanwhowe\BinaryTranscoder($key_array);
        $decode = $tr->decodeInteger($encoded_int);

        $this->assertEquals($expected_decode, $decode);
    }

    /**
     * @test
     * @dataProvider transcodingProvider
     * @dataProvider forwardTranscodingTruePadProvider
     * @covers       \ryanwhowe\BinaryTranscoder::decodeInteger()
     * @covers       \ryanwhowe\BinaryTranscoder::__construct()
     * @covers       \ryanwhowe\BinaryTranscoder::convertProtectedIntToBin()
     * @covers       \ryanwhowe\BinaryTranscoder::convertStringToArray()
     * @covers       \ryanwhowe\BinaryTranscoder::determineMaxArrayLength()
     * @param $encoded_int
     * @param $expected_decode
     * @throws Exception
     */
    public function decodingTruePad($encoded_int, $expected_decode)
    {

        $key_array = array_keys($expected_decode);
        $tr = new ryanwhowe\BinaryTranscoder($key_array, true);
        $decode = $tr->decodeInteger($encoded_int);

        $this->assertEquals($expected_decode, $decode);
    }

    /**
     * @test
     * @dataProvider transcodingProvider
     * @covers       \ryanwhowe\BinaryTranscoder::encodeArray()
     * @covers       \ryanwhowe\BinaryTranscoder::__construct()
     * @covers       \ryanwhowe\BinaryTranscoder::convertArrayToString()
     * @covers       \ryanwhowe\BinaryTranscoder::convertBinToProtectedInt()
     * @covers       \ryanwhowe\BinaryTranscoder::determineMaxArrayLength()
     * @param $expected_encode_int
     * @param $test_array
     * @throws \Exception
     */
    public function encoding($expected_encode_int, $test_array)
    {

        $tr = new ryanwhowe\BinaryTranscoder($test_array);
        $encoded = $tr->encodeArray($test_array);

        $this->assertEquals($expected_encode_int, $encoded);
    }

    /**
     * @test
     * @expectedException \ryanwhowe\BinaryTranscoderException
     * @covers \ryanwhowe\BinaryTranscoder::__construct()
     * @covers \ryanwhowe\BinaryTranscoder::determineMaxArrayLength
     * @throws \Exception
     */
    public function maxItemCountOverFlow()
    {
        $array_count = strlen(decbin(PHP_INT_MAX));
        $test_array = array();
        for ($i = 1; $i <= $array_count; $i++) {
            $test_array[$i] = false;
        }
        new \ryanwhowe\BinaryTranscoder($test_array);
    }

    /**
     * @test
     * @dataProvider             transcodingProvider
     * @covers                   \ryanwhowe\BinaryTranscoder::encodeArray()
     * @covers                   \ryanwhowe\BinaryTranscoder::__construct
     * @covers                   \ryanwhowe\BinaryTranscoder::convertArrayToString
     * @covers                   \ryanwhowe\BinaryTranscoder::determineMaxArrayLength
     * @param $expected_encode_int
     * @param $test_array
     * @expectedException \ryanwhowe\BinaryTranscoderException
     * @expectedExceptionMessage The source array has a different length than the output string!
     * @throws Exception
     */
    public function itemCountMissMatchEncoder($expected_encode_int, $test_array)
    {
        $tr = new ryanwhowe\BinaryTranscoder($test_array);
        array_push($test_array, array('key_z' => false));
        $tr->encodeArray($test_array);
    }

    /**
     * @test
     * @dataProvider             transcodingProvider
     * @dataProvider             forwardTranscodingFalsePadProvider
     * @expectedException \ryanwhowe\BinaryTranscoderException
     * @expectedExceptionMessage The passed string does not have enough elements for the key array
     * @covers                   \ryanwhowe\BinaryTranscoder::decodeInteger()
     * @covers                   \ryanwhowe\BinaryTranscoder::__construct
     * @covers                   \ryanwhowe\BinaryTranscoder::convertProtectedIntToBin
     * @covers                   \ryanwhowe\BinaryTranscoder::convertStringToArray
     * @covers                   \ryanwhowe\BinaryTranscoder::determineMaxArrayLength
     * @param $encoded_int
     * @param $expected_decode
     * @throws \Exception
     */
    public function itemCountMissMatchDecoder($encoded_int, $expected_decode)
    {
        $key_array = array_keys($expected_decode);
        $tr = new ryanwhowe\BinaryTranscoder($key_array);
        $failure_decode = bindec(str_pad('1', count($key_array) + 2, '0', STR_PAD_RIGHT));
        $decode = $tr->decodeInteger($failure_decode);
        $this->assertEquals($expected_decode, $decode);
    }
}