# php-binarytranscoder
Convert integer values to binary arrays in a backwards compatible safe method

While that might sound like a lot it is simply storing a php array of boolean values in an integer.  This integer can be
stored in a database or by some other means.  The array of booleans can be added to and it will be backwards compatible 
with the stored integer representation of the boolean array.

### Warning:
If you use this to store information into a database, this is stored in a nice compact form, however you need to keep in 
mind that it is stored in a form that make direct querying very difficult.  For example, if you stored a set of user 
permissions as an integer, it would be difficult to directly query the database for a list of users who have a given 
permission 'X' .

# Basic Usage
## Encoding
To encoding an array is to transform the representation of the array of boolean values to an integer representation for storage.
```php
$raw_array = array(
    'value 1' => true,
    'value 2' => false,
    'value 3' => true
);

$transcoder = new \ryanwhowe\BinaryTranscoder(array_keys($raw_array));
$encoded_value = $transcoder->encodeArray($raw_array);
var_dump($encoded_value);
```
output
```text
int(13)
```
## Decoding
decoding a value back to an array
```php
$output_array_key_values = array(
    'value 1',
    'value 2',
    'value 3'
);
$encoded_stored_value = 13;

$transcoder = new \ryanwhowe\BinaryTranscoder($output_array_key_values);
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
# Padding
The default behavior of the transcoder is to have any newly added array key default to null if there was no encoded 
value for that key.  This behavior can be altered to default to true when instantiating the object
```php
$transcoder = new \ryanwhowe\BinaryTranscoder($array_keys, \ryanwhowe\BinaryTranscoder::BOOLEAN_PAD_TRUE);
```


# Future 
Here are some additional plans that I am working on any may appear in future releases.
## todo
- [ ] Additional testing for instantiation
- [ ] Complete documentation on class methods
- [ ] Workout bitwise operations to make searches easier
- [ ] Additional test cases
- [ ] Explore converting this package to a php extension