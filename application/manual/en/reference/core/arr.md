# Array Operations

The array helper class provides commonly used array operations and are wrapped into a helper class. Available methods are:

* [is_assoc](#is_assoc) - Tests if an array is associative or not
* [path](#path) - Gets a value from an array using a dot separated path
* [range](#range) - Fill an array with a range of numbers
* [get](#get) - Retrieve a single key from an array and provides default when not found
* [extract](#extract) - Retrieves multiple keys from an array and provides default when not found
* [unshift](#unshift) - Adds a value to the beginning of an associative array
* [map](#map) - Recursive version of [array_map](http://php.net/array_map)
* [merge](#merge) - Merges one or more arrays recursively and preserves all keys
* [overwrite](#overwrite) - Overwrites an array with values from input arrays
* [callback](#callback) - Creates a callable function and parameter list from a string representation
* [flatten](#flatten) - Convert a multi-dimensional array into a single-dimensional array

## Test associative array {#is_assoc}

`Arr::is_assoc` tests an array if it is associative or not. Returns true when associative.

	$post = array(
		'username'	=> 'john.doe',
		'password'	=> 'secret'
	);
	
	// Returns true
	Arr::is_assoc($post);

	$list_str = array('foo', 'bar');
	$list_num = array(1, 6, 12);
	
	// Returns false
	Arr::is_assoc($list_str);
	Arr::is_assoc($list_num);

## Array using dot-separated path {#path}

`Arr::path` returns a value from an array using a dot separated path notation.

	$post = array(
		'user'		=> array(
			'id'		=> 'john.doe',
			'name'		=> 'John Doe II'
		),
		'via'		=> array(
			'id'		=> 'mobile',
			'name'		=> 'Mobile API'
		)
	);
	
	// Returns "john.doe"
	$user_id = Arr::path($post, 'user.id');
	
	// Returns "Mobile API"
	$posted_from = Arr::path($post, 'via.name');
	
Using the wildcard "*" will search intermediate arrays and return an array.

	$post = array(
		'user'		=> array(
			'id'		=> 'john.doe',
			'name'		=> 'John Doe II'
		),
		'via'		=> array(
			'id'		=> 'mobile',
			'name'		=> 'Mobile API'
		),
		'thumbs'	=> array(
			0			=> array(
				'width'		=> 100,
				'height'	=> 50,
				'url'		=> 'http://media.wx.yz/c4d.jpg'
			),
			1			=> array(
				'width'		=> 110,
				'height'	=> 50,
				'url'		=> 'http://media.wx.yz/20b.jpg'
			)
		)
	);
	
	// Returns all thumbnail URLs as an array
	$thumbnails = Arr::path($post, 'thumbs.*.url');

## Creating array range {#range}

`Arr::range` fills an array with a range of number. It accepts step value as first parameter (defaults to 10) and maximum value parameter (defaults to 100).

	// Fill an array with values 10, 20, 30, 40, 50, 60, 70, 80, 90, 100
	$values = Arr::range();

	// Fill an array with values 5, 10, 15, 20
	$values = Arr::range(5, 20)

## Getting array value and provide default {#get}

`Arr::get` retrieves a value from an array using a key. If the key does not exists in the array, a default value is returned instead.

To get the username from a `$_POST` array, we write:

	$username = Arr::get($_POST, 'username');

which will return the username if exists, otherwise `null` is returned.

A third parameter can be used to specify the default value.

	$post = array(
		'username'	=> 'john.doe',
		'email'		=> 'john.doe@domain.com'
	);
	
	// Returns 0 because subscribe does not exists in the array
	$subscribe = Arr::get($post, 'subscribe', 0);

## Getting multiple array values and provide default {#extract}

`Arr::extract` retrives multiple values from an array using an array of keys. If the key does not exists in the array, a default value is set for each key and the resulting array is returned.

To get the username and password from $_POST array, we write:

	// $_POST = array(
	//		'username'	=> 'john.doe',
	//		'password'	=> 'secret'
	//		'token'		=> '_secret_token_123'
	// );
	
	// Returns an array containing user and password
	$credentials = Arr::extract($_POST, array('username', 'password'));

which will return an associative array containing `username` and `password`. From the example above, the result will be:

	array(
		'username'	=> 'john.doe',
		'password'	=> 'secret'
	)
	
If a key is not found from the $_POST array, `null` value is used instead for every missing key. From the example above, if `password` is missing, the resulting array would be:

	array(
		'username'	=> 'john.doe',
		'password'	=> null
	)

If we want to use other values for default other than `null`, we may write:

	$credentials = Arr::extract($_POST, array('username', 'password'), '');

The above example uses an empty string for default value. 

[!!] The default value will be applied to all supplied keys. 

## Adding value to the beginning of array {#unshift}

`Arr::unshift` adds a value to the beginning of an array. It accepts three parameters: 1.) the array to modify 2.) the key to add and 3.) the value.

[!!] The array is passed by reference so the original array will be modified.

	$array = array(
		'name' 	=> 'John Doe',
		'email'	=> 'john@example.com'
	);

	Arr::unshift($array, 'id', 'john.doe');

The new value of `$array` now is:

	array(
		'id'	=> 'john.doe',
		'name'	=> 'John Doe',
		'email'	=> 'john@example.com'
	)

## Recursive array_map {#map}

`Arr::map` is a recursive version of [array_map](http://php.net/array_map), which applies the same callback to all elements in an array, including sub-arrays.

[!!] Unlike `array_map`, this method requires a callback and will only map a single array.

Our first example is using a multi-dimensional array of strings and we want trim trailing whitespace on each element of the array. We will use `trim` as the callback here.

	// Sample data with values having trailing whitespace
	$data = array(
		'post'	=> array(
			'from'	=> '   Mobile API   ',
			'via'	=> '  Some Provider  '
		),
		'date_posted' => '   2010-08-24  '
	);

	// Trim all values recursively
	$data = Arr:map('trim', $data);

The value of `$data` now looks like this:


	$data = array(
		'post'	=> array(
			'from'	=> 'Mobile API',
			'via'	=> 'Some Provider'
		),
		'date_posted' => '2010-08-24'
	);

Our next example uses a custom callback where a callback is a method of a class. Assuming we have a callback class like this:

	class ArrTest_Callback
	{
		public function to_int($value)
		{
			return (int) $value;
		}
	}

`ArrTest_Callback` has a method `to_int` which casts a value to integer and return the result. 

	$data = array(
		'total'		=> '100',
		'details'	=> array(
			'food'		=> '50',
			'drinks'	=> '',
			'tip'		=> 50,
			'extra'		=> null
		)
	);

	$testCallback = new ArrTest_Callback;
	$data = Arr::map(array($testCallback, 'to_int'), $data);

To pass a class method as a callback, a callback must be in an array form: the first element is the object and the second element is the method name. Our data now contains all integers.

	$data = array(
		'total'		=> 100,
		'details'	=> array(
			'food'		=> 50,
			'drinks'	=> 0,
			'tip'		=> 50,
			'extra'		=> 0
		)
	);

## Merge arrays recursively {#merge}

`Arr::merge` merges arrays recursively and preserves all keys.

[!!] This is not the same as [array_merge_recursive](http://php.net/array_merge_recursive)!

	$john = array(
		'name' => 'john', 
		'children' => array(
			'fred', 
			'paul', 
			'sally', 
			'jane'
		)
	);

	$mary = array(
		'name' => 'mary', 
		'children' => array('jane')
	);
 
	// John and Mary are married, merge them together
	$merged = Arr::merge($john, $mary);
	 
The output of `$merged` will now be:

	array(
		'name' => 'mary',
		'children' => array(
			'fred',
			'paul',
			'sally', 
			'jane'
		)
	)

`Arr::merge` accepts indefinite number of array input. 

	$merged = Arr::merge($ar1, $ar2, $ar3, $ar4, $ar5);

## Overwrite array with other array {#overwrite}

`Arr::overwrite` overwrites an array with input arrays. It accepts a minimum of two parameters. First parameter is the master array to be overwritten. The rest of the parameters are also arrays that will overwrite the master array.

[!!] Keys that do not exist in the first array will not be added!

     $a1 = array('name' => 'john', 'mood' => 'happy', 'food' => 'bacon');
     $a2 = array('name' => 'jack', 'food' => 'tacos', 'drink' => 'beer');

     // Overwrite the values of $a1 with $a2
     $array = Arr::overwrite($a1, $a2);

     // The output of $array will now be:
     array('name' => 'jack', 'mood' => 'happy', 'food' => 'bacon')

