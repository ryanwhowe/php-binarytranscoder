# php-binarytranscoder

[![GitHub release](https://img.shields.io/github/release/ryanwhowe/php-binarytranscoder.svg)](https://github.com/ryanwhowe/php-binarytranscoder)

### WARNING!!!

#### This Is a Breaking Change from the v1 branch

The encoder and decoder have been changed to reverse the binary string that transcoded from the input array.  This will 
allow for bitwise searches of the encoded integer values in a backwards compatible way.  This will cause a breaking 
change from the v1 branch if this is used to decode any integers encoded by the v1 package.  Any information encoded by
the v1 package will need to be recoded.

Convert integer values to binary arrays in a backwards compatible safe method

While that might sound like a lot it is simply storing a php array of boolean values in an integer.  This integer can be
stored in a database or by some other means.  The array of booleans can be added to and it will be backwards compatible 
with the stored integer representation of the boolean array.

### Warning:
If you use this to store information into a database, this is stored in a nice compact form, however you need to keep in 
mind that it is stored in a form that make direct querying very difficult.  For example, if you stored a set of user 
permissions as an integer, it would be difficult to directly query the database for a list of users who have a given 
permission 'X' .

## Basic Usage
### Encoding
To encoding an array is to transform the representation of the array of boolean values to an integer representation for storage.
```php
$raw_array = array(
    'value 1' => true,
    'value 2' => false,
    'value 3' => true
);

$transcoder = new \ryanwhowe\BinaryTranscoder($raw_array);
$encoded_value = $transcoder->encodeArray($raw_array);
var_dump($encoded_value);
```
output
```text
int(13)
```
### Decoding
decoding a value back to an array
```php
use RyanWHowe\BinaryTranscoder\BinaryTranscoder;
$output_array_key_values = array(
    'value 1',
    'value 2',
    'value 3'
);
$encoded_stored_value = 13;

$transcoder = new BinaryTranscoder($output_array_key_values);
$result = $transcoder->decodeInteger($encoded_stored_value);
var_dump($result);

```
output
```text
array(3) {
  'value 1' => bool(true)
  'value 2' => bool(false)
  'value 3' => bool(true)
}
```

### Max Integer Value (new v2.0)
If storing the trancoded integer value an implementation may need to limit the maximum allowable integer value to 
something other than the default PHP_INT_MAX value.  **Important:** you can **NOT** exceed the PHP_INT_MAX value as this 
is a limitation of PHP.  The example below limits to an unsigned 32 bit integer value (the max for a MySQL UNSIGNED INT column)
```php
use RyanWHowe\BinaryTranscoder\BinaryTranscoder;
$transcoder = new BinaryTranscoder($array_keys, 4294967295);
```
### Padding
The default behavior of the transcoder is to have any newly added array key default to false if there was no encoded 
value for that key.  This behavior can be altered to default to true when instantiating the object, or to null.
```php
use RyanWHowe\BinaryTranscoder\BinaryTranscoder;
$transcoder = new BinaryTranscoder($array_keys, PHP_INT_MAX, \ryanwhowe\BinaryTranscoder::BOOLEAN_PAD_TRUE);
```

## Advanced Usage
### Max Int
The class will check the system **PHP_INT_MAX** size when instantiated.  This is through the static method 
_determineMaxArrayLength()_ which accepts an optional integer parameter to allow you to determine the columns that can be
encoded from a source array.  This utility function can be used with the documented limits of a particular database 
storage type to ensure you are not exceeding you encoding abilities.  In general you will always have 1 fewer storage 
positions than you have bits in the integer that is being encoded (see [methodology](#methodology) for more detail).

As of version 2.0 the max integer value can be set to something lower than **PHP_INT_MAX**.  This can be useful if 
storing the value in a 32bit integer, i.e. MySQL INT

#### Warning
Regardless of the storage size limitations you can not exceed your systems **PHP_INT_MAX** size!

#### example
```php
use RyanWHowe\BinaryTranscoder\BinaryTranscoder;
$mysql_small_unsigned_int_max = 65535;
$max_columns = BinaryTranscoder::determineMaxArrayLength($mysql_small_unsigned_int_max);
var_dump($max_columns);
```
output
```text
int(15)
```

## Methodology
The information is translated from a boolean array to a binary string converting true values to 1 and false values to 0.
In order to ensure the decoding process there is an additional most significant bit added to the beginning of the binary 
string.  This string is also reversed so that the oldest values are at the end of the string.  This is for bitwise 
searching of the data.
````text
[false, false, true, false] would become 10100 NOT 0100 
````
This creates a minimum value that will be stored, even for a completely false set of array values, which will be a 
constant for the count of the array being used.  This also provides a "versioning" of the stored integer value.

The best use case for the class is to have it be a resource of another class that has control over the array that will 
be encoded and decoded into.
