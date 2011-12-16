<?php
/**
 * RavePHP is a lightweight PHP utility class that you can dance to. It provides 
 * formatting methods to help you mix HTML/JS/PHP like an underground DJ.
 * @author Ryan Van Etten (c) 2011
 * @license https://github.com/ryanve/ravephp
 * @requires PHP 5.2.3+
 * @version 0.9.0
 * @license MIT
 */
 
if (!class_exists('Rave')) {
class Rave {

	/**
	 * Rave::is_human        Test if an unknown variable is a type for humans: string or numeric.
	 *
	 * @param    mixed       $ukn is the unknown that you want to test.
	 * @return   boolean     true  for strings|numbers (including '' and 0)
	 *                       false for arrays|objects|null|booleans
	 */
	public static function is_human($ukn) {
		// Equivalent to ( is_string($ukn) || is_numeric($ukn) ) but faster.
		// http://dev.airve.com/demo/speed_tests/php_is_scalar_not_bool.php
		return isset($ukn) && (is_string($ukn) || is_numeric($ukn)); // boolean
	}


	/**
	 * Rave::humanize            Test input w/ Rave::is_human. If human, return it. If not, return ''.
	 *
	 * @param   mixed            $ukn is a variable of unknown type you want to humanize.
	 * @return  string|number    the original input, or '' if input was not string|number.
	 *
	 * @example 
	 *  Rave::humanize(1000)      #  1000
	 *  Rave::humanize('dj')      #  'dj'
	 *  Rave::humanize(array(8))  #  ''
	 *  Rave::humanize(null)      #  ''
	 *  Rave::humanize(true)      #  ''
	 *  Rave::humanize(0)         #  0
	 */
	public static function humanize($ukn) {
		return self::is_human($ukn) ? $ukn : '';
	}


	/**
	 * Rave::can_breakitdown      Test if an unknown variable can be used as a delimiter, such 
	 *                            as you'd need to explode a string (break it down into parts).
	 *                            Equivalent to Rave::is_human except when used on '' or "".
	 * @param   mixed     $ukn    is the unknown variable you want to test.
	 * @return  boolean           true for numbers|strings (except ''), false otherwise.
	 *
	 * @example
	 *  Rave::is_human('')              // true 
	 *  Rave::can_breakitdown('')       // false
	 *  Rave::can_breakitdown('-')      // true
	 *  Rave::can_breakitdown('Yy')     // true
	 *  Rave::can_breakitdown(1000)     // true
	 *  Rave::can_breakitdown(true)     // false
	 *  Rave::can_breakitdown(false)    // false
	 *  Rave::can_breakitdown(array(8)) // false
	 *  Rave::can_breakitdown(1.5)      // true
	 *  Rave::can_breakitdown(0)        // true
	 */
	public static function can_breakitdown($ukn) {
		return self::is_human($ukn) && '' !== $ukn; // boolean
	}


	/**
	 * Rave::is_dust             Test for strings that consist only of punctuation and/or whitespace characters.
	 *                           
	 * @param   mixed            $ukn   is the unknown variable to test.
	 * @return  boolean          true  for strings that contain only whitespace or punctuation characters.
	 *                           false for non-strings and strings containing letters or numbers.
	 *                           
	 */	
	public static function is_dust($ukn) {
		// In the third test, replace whitespaces with # to make them punctuation so they'll count
		// as dust. The array was derived from the list @link php.net/manual/en/function.trim.php
		$whitespace = array(' ', "\s", "\t", "\n", "\r", "\0", "\x0B");
		return isset($ukn) && is_string($ukn) && ctype_punct(str_replace($whitespace, '#', $ukn));
	}


	/**
	 * Rave::is_void         Test for empty inputs, pure whitespace, or non strings.
	 *                           
	 * @param   mixed     $ukn   is the unknown variable or form input to test.
	 * @return  boolean          true for whitespace|numeric|object|array|boolean|null
	 *                           false for non-empty non-whitespace strings
	 *                           
	 */
	public static function is_void($ukn) {
		
		return !isset($ukn) || !is_string($ukn) || '' === trim($ukn); // boolean
	}


	/**
	 * Rave::is_literal
	 *
	 * @param    string|mixed
	 * @return   boolean
	*/
	public static function is_literal($ukn) {
		
		if ( !isset($ukn) ) {
			return false;
		}
		elseif ( is_numeric($ukn) ) {
			return true;
		}
		else {
			//                          true | false |  ''    |   ""   |   []   |   {}   |             function wrappers   /   anonymous functions  /  function literals              | undefined | null
			return 1 === preg_match( '/^true$|^false$|^\'.*\'$|^\".*\"$|^\[.*\]$|^\{.*\}$|^\s*((\$|jQuery)\([a-z]+\)\.[a-z]+)?\(?\s*function[a-z0-9_\s]*\(.*\}\s*\)?\s*\(?.*\)?\;?\s*$|^undefined$|^null$/i', trim((string)$ukn) );
		}
	}


	/**
	 * Rave::ok_id                     Test if a string is a valid identifier for data keys, CSS, or other
	 *                                 purposes. Allows letters (upper and lower), digits, dashes, and 
	 *                                 underscores. The string must start with either a letter or underscore. 
	 * @param   string|mixed   $ukn    is the unknown to test. 
 	 * @return  boolean                true  for alpanumeric|dash|underscore strings not starting w/ digit|dash.
	 *                                 false for non-strings, or strings that don't meet the above.
	 * @example
	 *  Rave::ok_id('yes_or_no')  #  true
	 *  Rave::ok_id('_House800')  #  true
	 *  Rave::ok_id('data-r480')  #  true
	 *  Rave::ok_id(0)            #  false
	 *  Rave::ok_id('')           #  false
	 *  Rave::ok_id(1000)         #  false
	 *  Rave::ok_id('        ')   #  false
	 *  Rave::ok_id('7seconds')   #  false
	 *  Rave::ok_id('yes&no')     #  false
	 *
	 */	
	public static function ok_id($ukn) {
		
		// First char must be letter or underscore. Allow alpanumeric|dash|underscore in the rest.
		return !self::is_void($ukn) && 0 === preg_match('/^[^a-zA-Z_]|[^a-zA-Z0-9_\-]/', $ukn);
	}


	/**
	 * Rave::ok_var              Check if input is an allowed name for a JavaScript variable.
	 * 
	 * @param   mixed     $ukn   is the unknown variable to test.
	 * @return  boolean          true if okay, false if not.
	 */	
	public static function ok_var($ukn) {
		
		// First char must be letter or underscore. Allow alpanumeric|underscore in the rest.
		return !self::is_void($ukn) && 0 === preg_match('/^[^a-zA-Z_]|[^a-zA-Z0-9_]/', $ukn);
	}


	/**
	 * Rave::to_var
	 *
	 * @param   string        $str
	 * @param   boolean       $camelcase
	 * @param   integer       $offset
	 * @return  string|false              
	 *
	 *
	 *
	 */		
	public static function to_var($str, $camelcase = false, $offset = 1) {
	
		if ( self::is_void($str) || !is_scalar($str) || true === $str ) {
			return false;
		}
				
		$str = strtolower((string)$str);           // Force lowercase.
		$parts = preg_split('/[^a-z0-9]+/', $str); // Split by anything not lowercase alphanumeric.
		$count = count($parts);                    // Count array parts.
		
		if ( ctype_digit($parts[0]) ) {    // If first char is a digit, then
			$parts[0] = '_' . $parts[0];   // prepend it w/ an underscore.
		}

		if ( true === $camelcase ) {
		
			if ( false === $offset ) {
				$offset = 0;  // WithoutOffest
			}
			elseif ( !is_int($offset) ) {
				$offset = 1;  // withOffset
			}
			
			for ($i = $offset; $i <= $count; $i++) {  // Start loop at $offest.
				$parts[$i] = ucfirst($parts[$i]);     // Uppercase first char.
			}	
			
			return implode($parts); // CamelCasedString
		}
		
		else {
			return implode('_', $parts); // Not CamelCase. Join  w/ underscore.
		}
	}


	/**
	 * Rave::cdata               Wrap code in CDATA tags.
	 *
	 * @param   string   $code   is the code (usually JavaScript) that you want to wrap.
	 * @return  string           is the wrapped code
	 */
	public static function cdata($code, $break = "\n", $indent = "\t") {
		
		return isset($code) ? '/*<![CDATA[*/' . $break . $code . $break . $indent . '/*]]>*/' : $code;
	}


	/**
	 * Rave::mirror                   Convert left bracket to right bracket or right bracket to left bracket.
	 *
	 * @param   string|mixed   $char  A single bracket (or parenthesis) to get the mirror for. Intended for 
	 *                                strings and will not affect other types.
	 * @return  string|mixed          the opposite bracket (or unchanged $char if not a bracket)
	 *
	 */
	public static function mirror($char) {
		
		$conversions = array( '[' => ']'
		                    , '{' => '}'
		                    , ']' => '['
		                    , '}' => '{'
		                    , '(' => ')'
		                    , ')' => '('
		                    , '<' => '>'
		                    , '>' => '<'
		                    );
		                    
		return isset($conversions[$char]) ? $conversions[$char] : $char;
	}


	/**
	 * Rave::sanitize                      Sanitize a string, with options.
	 *
	 * @param   string            $str     is the string you want to sanitize.
	 * @param   string            $space   is the whitespace replacement. By default '-' is used so that
	 *                                     whitespace is replaced by dashes. To allow whitespace, set to false.
	 * @param   callback|boolean  $filter  is an optional callback function to apply to $str. When omitted or set
	 *                                     to true, the it defaults to 'mb_strtolower' for forcing lowercase. You
	 *                                     can override w/ a different callback, or, for none, set to false.
 	 * @param   string            $other   is the replacement for other illegal characters. Default: ''
	 * @return  string|mixed               is the sanitized string (or the original input if it wasn't a string)
	 *                            
	 */
	public static function sanitize($str, $space = '-', $filter = true, $other = false) {
	
		if (!isset($str) || !is_string($str)) {
			return $str; // Return unchanged.
		}
		
		$str = trim($str); // Trim surrounding whitespace.
		
		if ( isset($filter) && is_callable($filter) ) {
			$str = call_user_func($filter, $str); // Call $filter.
		}
		elseif ( true === $filter ) {
			$str = mb_strtolower($str); // Call the default filter.
		}
		
		// Replace all inner whitespace chars w/ $space, provided $space is a string.
		isset($space) && is_string($space) and $str = preg_replace('/[ \s\t\n\r\0\x0B]+/', $space, $str);
		
		// If $other is not a string, reset it to an empty string.
		isset($other) && is_string($other) or $other = '';
		
		// Replace entities, then octets, then anything not alphanumeric|underscore|dash|space.
		return preg_replace('/&.+?;|%([a-fA-F0-9][a-fA-F0-9])|[^a-zA-Z0-9_\- \s]/', $other, $str);
	}

		
	/**
	 * Rave::pad
	 *
	 * @param   string|number   $str is the input that you want to pad. Inputs that aren't
	 *                          strings or numbers are ignored and returned as is (w/o error).
	 * @param   string|number   $left is the padding to add to the left. 
	 *
	 * @example #TODO
	 *  Rave::pad()  #
	 *  Rave::pad()  #
	 */
	public static function pad($str, $left = false, $right = true) {
	
		if ( !self::is_human($str) ) { 
			return $str;  // Return unchanged if wrong type.
		}
		
		$left = self::humanize($left);  // Ensure $left is string|numeric.
		
		if ( true !== $right ) {  // Handle when right is specified explicitly.
			return $left . $str . self::humanize($right); 
		}
		
		// Check for yins ({[< in any combination. 
		elseif (1 === preg_match('/[\[\(\{\<]+/', $left)) { // If found, match with yangs on the right.
			return $left . $str . implode(array_reverse(array_map('Rave::mirror', str_split($left))));
		}
		
		else { // Simply match right to left.
			return $left . $str . $left; 
		}
	}


	/** 
	 * Rave::quote                        Quote strings, except ones not meant to be quoted in Javascript. 
	 *                                    Intended for quoting PHP strings containing JavaScript code.
	 *                          
	 * @param    string|mixed   $code
	 * @param    string         $quote
	 * @return   string|mixed             the quoted string, or the original input if it wasn't a string, or if
	 *                                    it's a string representing a JavaScript literal (see: Rave::is_literal)
	 *
	 * @example #TODO
	 * 
	 *
	 */
	public static function quote($code, $quote = '"') {
		
		if ( !isset($code) || !is_string($code) || self::is_literal($code) ) { 
			return $code; // Return if not a string or if meant to be a literal.
		}
		
		else {
			$quote = substr($quote, 0, 1);                // Ensure $quote is only 1 character.
			return $quote . trim($code, $quote) . $quote; // Trim before joining to prevent quoting twice.
		}
	}


	/** 
	 * Rave::rebound                 Remove quotes that surround strings not needing quotes in JavaScript, such 
	 *                               as numbers|booleans|[Arrays]|{Objects}|null|undefined (case insensitive)
	 *                               anonymous functions, function literals, and common function wrappers.
	 *
	 * @param   string|mixed   $js
	 * @return  string|mixed
	 *
	 * @example
	 *  Rave::reboud("'{  }'")  #  "{  }"
	 *  Rave::reboud('"[  ]"')  #  '[  ]'
	 *  Rave::reboud('"true"')  #  'true'
	 *  Rave::reboud('"1000"')  #  '1000'
	 *
	 */
	public static function rebound($js) {
		//                     ( $1  )( ===================================================================  $2  ============================================================================ )( $3  )
		//                      quote   [arr]inner | {obj}inner | [arr]| {obj}|true|false| This part covers anonymous functions, function literals, and common function wrappers.   |null|undefined| +-numbers/decimals   quote
		return preg_replace( '/(\'|\")(\[[^\[\]]*\]|\{[^\}\}]*\}|\[.*\]|\{.*\}|true|false|\s*((\$|jQuery)\([a-z]+\)\.[a-z]+)?\(?\s*function[a-z0-9_\s]*\(.*\}\s*\)?\s*\(?.*\)?\;?\s*|null|undefined|\-?[0-9]*[\.]?[0-9]+)(\'|\")/i', '$2', (string)$js );
	}


	/**
	 * Rave::unfold                  Unfold a block of (JavaScript) code. (Add line breaks and tabs.)
	 *
	 * @param   string    $js        is the code (usually JavaScript) to unfold.
	 * @param   string    $break     is the linebreak between lines. Default: "\n"
	 * @param   string    $indent    is the indent before each line. Default: "\t"
	 * @param   string    $offset    is the overall indent.          Default: "\t"
	 * @param   string    $wrap      is the text to wrap the output. Default: "\n\t"
	 * @return  string               is the unfolded code.
	 * 
	 */
	public static function unfold($js, $break = "\n", $indent = "\t", $offset = "\t", $wrap = "\n\t") {
		
		$break .= $offset; // Keep offset param separate for future options.
		
		// Hmm...
		$replace = array( '},{' => $break . '}!comma! {' . $break . $indent
						, '([{' => '([{' . $break . $indent
						, '}])' => $break . '}])'
						, "',"  => "'," . $break . $indent 
						, "},"  => "}," . $break . $indent 
						, "],"  => "]," . $break . $indent 
						, '}!comma!'  => '},' 
						);
						
		foreach (array_keys($replace) as $needle) {
			$js = str_replace($needle, $replace[$needle], $js);
		}
		
		return $wrap . $js . $wrap;
	}


	/**
	 * Rave::affix                              Iterate through an array to prepend and/or append each 
	 *                                          of its values. Works like Rave::pad, but for arrays. It's
	 *                                          safe to use on mixed arrays, b/c values in the array that 
	 *                                          are not string|numeric are skipped. It also does the same
	 *                                          magic for opening brackets and parens that Rave::pad does.
	 * @param   string|number|boolean   $left   gets prepended to each of $arr's values.
	 *                                          For none, set to '' or false. If $left is a yin character
	 *                                          such as '(', '[', '{', or '<', or any series of yins and
	 *                                          $right is not specified, $left is mirrored on the right. 
	 * @param   array                   $arr    is the array you want to iterate through and return.
	 * @param   string|number|boolean   $right  gets appended to each of $arr's values.
	 *                                          When $right === true (default behavior), $right
	 *                                          will match $left. For none, set to '' or false.
	 *
	 * @return  array                           the updated array, or unchanged $arr for non-arrays.
	 *
	 */
	public static function affix($arr, $left = false, $right = true) {
		
		if ( isset($arr) && is_array($arr) ) {
			foreach ( $arr as &$a ) {
				$a = Rave::pad($a, $left, $right);
			}
		}
		
		return $arr;
	}


	/**
	 * Rave::to_array                Convert anything to an array. It's useful when sending an unknown 
	 *                               type to a loop or array function. Its best application is writing 
	 *                               functions that support mixed inputs.
	 * @param   mixed    $ukn        is the variable of unknown type that you to convert to an array.
	 * @param   string   $delimiter  is an optional delimiter used to explode string|numeric inputs.
	 *
	 * @return  array
	 *
	 * @example
	 *  Rave::to_array('abc')         #  array('abc')      // cast to array
	 *  Rave::to_array('abc', 'b')    #  array('a', 'c')   // exploded to array
	 *  Rave::to_array(array('abc'))  #  array('abc')      // already an array
	 *
	*/
	public static function to_array($ukn, $delimiter = false) {
	
		// Explode strings|numbers when delimiter is supplied.
		if (self::is_human($ukn) && self::can_breakitdown($delimiter)) { 
			return explode($delimiter, $ukn); 
		}
		
		// Convert remaining non-objects with casting operator.
		// Includes when UKN is already an array.
		elseif (!is_object($ukn)) { 
			return (array)$ukn; 
		}
		
		// For objects, convert with get_object_vars().
		else { 
			return get_object_vars($ukn); 
		}
	}


	/**
	 * Rave::dubstep 
	 * 
	 * @param    callback|true   $test             is the conditional test to do on each item of $array
	 *                                             before applying $callback. The $test should be a function 
	 *                                             that returns boolean, such as 'is_string'. Items that pass the
	 *                                             test are passed through the $callback. Ones that fail $test
	 *                                             are left as is. 
	 * @param    callback        $callback         is the callback to apply on $array items that pass $test.
	 * @param    array           $array            is the array to which you want to iterate through.
	 * @param    mixed           $arg1, $arg2...   are extra parameters to send to the $callback.
	 * @return   array
	 *
	 */
	public static function dubstep($test, $callback, $array) {
	
		// We want people to know if they're doing it wrong and why.
		if ( !isset($test) || !is_callable($test) || !isset($callback) || !is_callable($callback)  ) {
			trigger_error(__METHOD__ . ' parameters 1 and 2 must be callable ', E_USER_WARNING);
			return false;
		}
		elseif ( !isset($array) || !is_array($array) ) {
			trigger_error(__METHOD__ . ' parameter 3 must be an array ', E_USER_WARNING);
			return false;
		}
		elseif ( 4 > func_num_args() ) { // No extra args.
			foreach( $array as &$a ) {
				true === @call_user_func($test, $a) and $a = call_user_func($callback, $a);
			}
		}
		else { // Has extra args.
			$args = array_slice(func_get_args(), 3); 
			foreach( $array as &$a ) {
				if ( true === @call_user_func($test, $a) ) {
					$temp = $args; 
					array_shift($temp, $a);
					$a = call_user_func_array($callback, $temp);
				}
			}
		}
		return $array;
	}


	/**
	 * Rave::shake                  Removes null|whitespace|''|false values from an array. Shake does what
	 *                              what the default array_filter does and also removes whitespace strings.
	 *
	 * @param   array    $arr
	 * @return  array               the updated array
	 *
	 */
	public static function shake($arr) {
		
		return array_filter(self::dubstep('is_string', 'trim', $arr));
	}


	/**
	 * Rave::remix          TODO
	 *
	 * @param   mixed       $arg1, $arg2...
	 * @return  array       the merged array
	 *
	 */
	public static function remix() {
		
		$args = func_get_args();
		$glue = self::is_dust($args[0]) ? array_shift($args) : false;
		
		// Map with Rave::to_array using glue as delimiter for exploding 
		// strings to arrays. This puts all the $args into array form.
		$args = array_map('Rave::to_array', $args, array($glue)); 
		
		// Merge the arrays into one.
		return call_user_func_array('array_merge', $args); 
	}


	/**
	 * Rave::hook_up                                TODO
	 *                                              ...it removes duplicates before joining.
	 * @param  	string|number  $glue                is the glue to use to to join the pieces. Default: ''
	 *                                              The glue is the 1st parameter and is required. To
	 *                                              join w/o glue, use false or '' as the 1st parameter.
	 * @param  	mixed          $lips1, $lips2...    can be a mix of arrays, strings, or numbers to join.
  	 * @return  string
	 */
	public static function hook_up() {
	
		// Get inputs as array. Remove first arg for glue.
		$args = func_get_args();                    
		$glue = self::humanize(array_shift($args));
		
		// Map with Rave::to_array using glue as delimiter for exploding 
		// strings to arrays. This puts all the $args into array form.
		$args = array_map('Rave::to_array', $args, array($glue)); 
		
		// Merge the arrays into one. Then shake out null|whitespace|''|false values.
		$args = self::shake(call_user_func_array('array_merge', $args)); 
		
		// Remove duplicates and join the values into a string connected by $glue.
		return implode($glue, array_unique($args));
	}


	/**
	 * Rave::bump                            TODO
	 *
	 * @param   array            $arr
	 * @param   string           $separator
	 * @param   string|boolean   $after
	 * @param   string|boolean   $before
	 */
	public static function bump($arr, $separator = false, $after = true, $before = false) {
	
		// Reset separator to '' if not string|number one char or longer.
		self::can_breakitdown($separator) or $separator = ''; 
		
		if (1 === strlen($separator)) {
			$first = $last = $separator; // First and last character are the same.
		}
		else {
			$first = substr($separator, 0, 1); // First char.
			$last = substr($separator, -1, 1); // Last char.		
		}
		
		$before = true === $before ? self::mirror($first) : self::humanize($before); 
		$after  = true === $after  ? self::mirror($last)  : self::humanize($after); 
		
		$bump = array(); // Declare new array that'll get filled in the loop.
		
		// Loop the keys b/c we need each key and its corresponding value.
		foreach (array_keys($arr) as $key) {
			$bump[] = $before . $key . $separator . $arr[$key] . $after; 
		}
		
		return $bump; // array
	}


	/**
	 * Rave::bump_and_join              Do Rave::bump and then join (implode).
	 *
	 * @param   string|number   $glue   Is the implode glue. (php.net/manual/en/function.implode.php)
	 * @return  string  
	 */
	public static function bump_and_join($glue = '', $arr, $separator = false, $after = true, $before = false) {
		
		return implode((string)$glue, self::bump($arr, $separator, $after, $before)); // string
	}


	## Methods below here are in development and need testing ##
	
	/**
	 * Rave::data_to_js                Convert data to JavaScript.
	 *
	 * @param   array     $data        is a one-dimensional array of data that you want to convert.
	 *                                 (Use Rave::each_to_js for multidimensional arrays.)
	 * @param   string    $grouping	   is a single opening bracket, either '{' or '[' to represent whether
	 *                                 the output should be an object or array. When '{' is used the 
	 *                                 array keys of $data become object property names. Default: '['
	 * @param   integer   $quote       is the quote type to use on strings. Use 1 for single quotes,
	 *                                 or 2 for double quotes. Defaults to double quotes. Strings that
	 *                                 represent literals such as functions, '[arrays]', {objects}, 
	 *                                 numbers, 'true', 'undefined', 'false', 'null' are not quoted.
	 *                                 
	 * @return  string|false           formatted JavaScript, or false for invalid input.
	 */
	public static function data_to_js($data, $grouping = '[', $quote = '"') {
	
		if ( !isset($data) || !is_array($data) ) {
			return false; 
		}
		
		// Remove invalid grouping chars. Only [ and { brackets are allowed.
		$grouping = preg_replace('/[^\[\{]/', '', (string)$grouping);
		
		$chars = array( 1 => "'", 2 => '"' );  // Allowed quote characters. (single or double)
		$quote = isset($chars[$quote]) ? $chars[$quote] : $chars[2]; // Default to 2. (double)
		
		if ( '{' === $grouping ) { // Objects
			$data = self::bump($data, ': ' . $quote);
		}
		
		else {
			$grouping = '['; // Other Types => Array
			$data = self::affix($data, $quote, $quote);
		}
	
		return self::rebound( $grouping . implode(', ', $data) . self::mirror($grouping) );
	}


	/**
	 * Rave::each_to_js                Multidimensional version of Rave::data_to_js
	 *
	 * @param   array     $data        is a multidimensional array.
	 * @param   string    $grouping	   is a series of opening brackets, such as {[, [[, or [{{ that
	 *                                 represents the structure of the desired JavaScript output. The
	 *                                 number or brackets should equal the depth of $arr. When { is used
	 *                                 the array keys from that level become object property names.
	 * @param   integer   $quote       is the quote type to use on strings. Use 1 for single quotes,
	 *                                 or 2 for double quotes. Defaults to double quotes. Strings that
	 *                                 represent literals such as functions, '[arrays]', {objects}, 
	 *                                 numbers, 'true', 'undefined', 'false', 'null' are not quoted.
	 
	 * @return  string|false           formatted JavaScript or false for invalid input.
	 * 
	 */
	public static function each_to_js($data, $grouping = '[', $quote = '"') {
	
		if ( !isset($data) || !is_array($data) ) {
			trigger_error(__METHOD__ . ' parameter 1 must be an array ', E_USER_WARNING);
			return false; 
		}
		
		// Remove invalid grouping chars. Only [ and { brackets are allowed.
		$grouping = preg_replace('/[^\[\{]/', '', (string)$grouping);
		
		if ( '' === $grouping ) {
			trigger_error(__METHOD__ . ' parameter 2 must be a series of opening brackets such as {[, [[, or [{{'
			                         . ' that represents the structure of the desired output ', E_USER_WARNING);
			return false;
		}
		
		else {
			$grouping = str_split($grouping);  // Split string into an array.
			$count = count($grouping);         // Count array items.
		}
		
		// The part below doesn't completely work. It could be done with a more complicated
		// loop and use of array_walk_recursive, but we should remove this function and
		// instead use PHP's built-in json_encode() function for this sort of thing.
		
		// Loop backwards through $grouping (working in to out).
		// Stop at index 2 so we can do the outermost pass separately.
		for ($i = $count; $i >= 2; $i--) {
			// Pass items of $data that are arrays through Rave::data_to_js
			foreach ( $data as &$a ) {
				isset($a) && is_array($a) and $a = self::data_to_js($a, $grouping[$i], $quote);
			}
		}
		
		// Pass the updated outer array through Rave::data_to_js
		return self::data_to_js($data, $grouping[$i], $quote); // string
		
	}


	/**
	 * Rave::data_to_json               Convert data to JSON.
	 *
	 * @param   mixed         $data     is the data you want to convert into JSON. It can be any type except a
	 *                                  resource. Array keys on associative arrays become JSON property names.
	 * @param   string|false  $attr  
	 * @param   int|flag      $options  is the $options parameter for json_encode()
	 * @return  string
	 */
	public static function data_to_json($data, $attr = false, $options = 0) {
		$json = json_encode($json, $options);
		return self::ok_id($attr) ? $attr . "='" . $json . "'" : $json;
	}

}//class
}//if

// If RavePHP loads twice, we're fine b/c the above will only run once (even if
// loaded from multiple paths). But if a different class Rave exists, warn them:
elseif (class_exists('Rave') && !is_callable('Rave::data_to_js')) {
	trigger_error('RavePHP cannot load because another Rave class exists.'
	             , E_USER_WARNING
				 );
}

// End.