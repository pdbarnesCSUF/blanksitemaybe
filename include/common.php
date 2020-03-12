<?php
/****************
 * Common generalized utilities functions
 ****************/
function read_log($file, $lines)
{
	if (file_exists($file))
	{
		//global $fsize;
		$handle = fopen($file, "r");
		$linecounter = $lines;
		$pos = -2;
		$beginning = false;
		$text = array();
		while ($linecounter > 0) {
			$t = " ";
			while ($t != "\n") {
				if(fseek($handle, $pos, SEEK_END) == -1) {
					$beginning = true; 
					break; 
				}
				$t = fgetc($handle);
				$pos --;
			}
			$linecounter --;
			if ($beginning) {
				rewind($handle);
			}
			$text[$lines-$linecounter-1] = fgets($handle);
			if ($beginning) break;
		}
		fclose ($handle);
		return array_reverse($text);
	}
	else
		return array("Cannot Access File");
}//read_log function
/**
 *  @brief Converts a string to a boolean variable
 *  
 *  @param [in] $var A string variable to convert
 *  @return Boolean value
 *  @see http://www.php.net/manual/en/function.is-bool.php#113693
 *  @details Converts a string to a boolean variable. Useful for converting database boolean to php boolean.
 */
function toBool($var) {
  if (!is_string($var)) return (bool) $var;
  switch (strtolower($var)) {
    case '1':
    case 'true':
	case 't':
    case 'on':
    case 'yes':
    case 'y':
      return true;
    default:
      return false;
  }
}
/**
 *  @brief Converts boolean to postgresql boolean in string format
 *  
 *  @param [in] $var A boolean variable to convert
 *  @return Boolean value as a string
 *  
 *  @details Converts a boolean to a string. Useful for converting php boolean to psql boolean.
 */
function toBool_psql($var) {
	if (!is_bool($var))
	{
		switch (strtolower($var))
		{
			case '1':
			case 'true':
			case 't':
			case 'on':
			case 'yes':
			case 'y':
			  return 'true';
			default:
			  return 'false';
		}
	}
	if ($var)
	{
		return 'true';
	}
	else
	{
		return 'false';
	}
}
/**
 *  @brief Converts integer to null if blank
 *  
 *  @param [in] $var A integer variable to convert
 *  @return null or number
 *  
 *  @details Convert integer to null if blank. pgsql does not take '' for null integers
 */
function int_pgsql($var) {
	if (!is_bool($var))
	{
		switch (strtolower($var))
		{
			case '1':
			case 'true':
			case 't':
			case 'on':
			case 'yes':
			case 'y':
			  return true;
			default:
			  return false;
		}
	}
	if ($var)
	{
		return 'true';
	}
	else
	{
		return 'false';
	}
}
/**
 *  @brief Checks if a prepared statement exists in the database transaction
 *  
 *  @param [in] $statement Name of the prepared statement
 *  @return Boolean if it exists
 *  
 *  @deprecated no more query names, using PDO system
 *  @details Checks if a prepared statement exists in the database transaction.
 */
/*
//DEPRECATED, make sure you don't NEED to use this
function query_exists($statement)
{
	$qrParamExist = pg_query_params("SELECT name FROM pg_prepared_statements WHERE name = $1", array($statement));
    if($qrParamExist){
        if(pg_num_rows($qrParamExist) != 0){
            return true;
        }
		else
		{
            return false;
        }
    }
}
*/
/*
 * 
 * name: execInBackground
 * @brief Runs a command in the system background
 * @param command to execute
 * @see http://php.net/manual/en/function.exec.php#86329
 */

function execInBackground($cmd) {
    if (substr(php_uname(), 0, 7) == "Windows"){
        pclose(popen("start /B ". $cmd, "r")); 
    }
    else {
        exec($cmd . " > /dev/null &");  
    }
} 
/**
 *  @brief Validates a time day string
 *  
 *  @param [in] $date date string to validateDate
 *  @param [in] $format format string that date string will be in
 *  @return Boolean - true if valid
 *  
 *  @details Validates a date/tiem string by putting it into a DateTime type and seeing if equivalanet, returns a boolean
 */
function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/*
 * 
 * name: return_bytes
 * @brief accepts a string with number followed by size character and returns the size in bytes. Such as "2m".
 * @param string with number followed by size character
 * @return number of bytes
 * @see http://php.net/manual/en/function.ini-get.php
 */
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

/*
 * 
 * name: clean_exec
 * @brief I THINK it removes escapecharacters
 * @param string with possible escape characters
 * @return cleaned string
 * 
 */

function clean_exec($val) {
	$str = preg_replace('/.{4}/','',$val); 
	$str2 = preg_replace('/.{3}/','',$str); //last on line
	return $str2;
}

/*
 * 
 * name: condense_spaces
 * @brief I THINK it removes consecutive spaces
 * @param string
 * @return string without repeated spaces
 * 
 */

function condense_spaces($val) {
	return preg_replace('/\s+/',' ',$val);
}
function imageCreateFromAny($filepath) {
	//$type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
	$info = getImageSize($filepath);
	switch($info['mime'])
	{
		case 'image/bmp' :
			$im = imagecreatefrombmp($filepath);
			break;
		case 'image/gif' :
			$im = imageCreateFromGif($filepath);
			break;
		case 'image/jpeg' :
			$im = imageCreateFromJpeg($filepath);
			break;
		case 'image/png' :
			$im = imageCreateFromPng($filepath);
			break;
		case 'image/wbmp' :
			$im = imagecreatefromwbmp($filepath);
			break;
		case 'image/xpm' :
			$im = imagecreatefromxpm($filepath);
			break;
		case 'image/xbm' :
			$im = imagecreatefromxbm($filepath);
			break;
		case 'image/webp' :
			$im = imagecreatefromwebp($filepath);
			break;
		default:
			$im = false;
			break;
	}   
	return $im; 
} 
?>
