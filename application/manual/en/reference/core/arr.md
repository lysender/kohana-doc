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

## Test Associative Array {#is_assoc}

`Arr::is_assoc` tests and array if it is associative or not. Returns true when associative.

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

## Array Using Dot Separated Path {#path}

`Arr::path` Returns a value from an array using a dot separated path notation. 

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
	$user_id = Arr:path($post, 'user.id');
	
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

## Creating Array Range

`Arr::range` fills an array with a range of number. It accepts step value as first parameter (defaults to 10) and maximum value parameter (defaults to 100).

	// Fill an array with values 10, 20, 30, 40, 50, 60, 70, 80, 90, 100
	$values = Arr::range();

	// Fill an array with values 5, 10, 15, 20
	$values = Arr::range(5, 20)

## Getting Array Value and Provide Default

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

## Getting Multiple Array Values and Provide Default

`Arr::extract` retrives multiple values from an array using an array of keys. If the key does not exists in the array, a default value is set for each key and the resulting array is returned.

To get the username and password from $_POST array, we write:

	// $_POST = array(
	//		'username'	=> 'john.doe',
	//		'password'	=> 'secret'
	//		'token'		=> '_secret_token_123'
	//	);
	
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

If we other values for default other than `null`, we may write:

	$credentials = Arr::extract($_POST, array('username', 'password'), '');

The above example uses an empty string for default value. 

[!!] The default value will be applied to all keys. Be sure you are not incorrectly using it for example: using default value `0` but some keys are string. In this case, `null` is the more applicable default.


