V2.0.0 ChangeLog
* Updated minimal requirements to php 7.0.0 for strict types
* Added CHANGELOG.md
* Added UPDATE_2.0.md
* Addded Custom Exception \ryanwhowe\BinaryTranscoderException
* Addded Custom Exception \ryanwhowe\BinaryTranscoderMaxLengthException
* Addded Custom Exception \ryanwhowe\BinaryTranscoderIntegerOverflowException
* BinaryTranscoder::__construct now throws a \ryanwhowe\BinaryTranscoderMaxLengthException when the number of elements that can be trasncoded exceeds the system amount available in an integer.
* BinaryTranscoder::__construct now throws a \ryanwhowe\BinaryTranscoderIntegerOverflowException when the maximum integer value parameter is set higher than PHP_INT_MAX.
* BinaryTranscoder::__construct now takes an optional maximum integer value parameter.
* BinaryTranscoder::__construct padding paramater has been moved to the third position after the maximum integer value parameter.
* BinaryTranscoder.decodeInteger() will now throw \ryanwhowe\BinaryTranscoderException when the passed integer is to small or two large for the key array used on instantiation.
* BinaryTranscoder.encodeArray() will now throw \ryanwhowe\BinaryTranscoderException when the passed array is of different length than the key array used on instantiation.
* Implemented strict types for all methods
* Updated tests for reverse encoding method
* Namespace changed from ryanwhowe to RyanWHowe\BinaryTranscoder
* Changed the package name from ryanwhowe/php-binarytranscoder to ryanwhowe/binarytranscoder
