<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
///                                                         //
// phpthumb.functions.php - general support functions       //
//                                                         ///
//////////////////////////////////////////////////////////////

class phpthumb_functions {

	function user_function_exists($functionname) {
		if (function_exists('get_defined_functions')) {
			static $get_defined_functions = array();
			if (empty($get_defined_functions)) {
				$get_defined_functions = get_defined_functions();
			}
			return in_array(strtolower($functionname), $get_defined_functions['user']);
		}
		return function_exists($functionname);
	}


	function builtin_function_exists($functionname) {
		if (function_exists('get_defined_functions')) {
			static $get_defined_functions = array();
			if (empty($get_defined_functions)) {
				$get_defined_functions = get_defined_functions();
			}
			return in_array(strtolower($functionname), $get_defined_functions['internal']);
		}
		return function_exists($functionname);
	}


	function version_compare_replacement_sub($version1, $version2, $operator='') {
		// If you specify the third optional operator argument, you can test for a particular relationship.
		// The possible operators are: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne respectively.
		// Using this argument, the function will return 1 if the relationship is the one specified by the operator, 0 otherwise.

		// If a part contains special version strings these are handled in the following order: dev < (alpha = a) < (beta = b) < RC < pl
		static $versiontype_lookup = array();
		if (empty($versiontype_lookup)) {
			$versiontype_lookup['dev']   = 10001;
			$versiontype_lookup['a']     = 10002;
			$versiontype_lookup['alpha'] = 10002;
			$versiontype_lookup['b']     = 10003;
			$versiontype_lookup['beta']  = 10003;
			$versiontype_lookup['RC']    = 10004;
			$versiontype_lookup['pl']    = 10005;
		}
		if (isset($versiontype_lookup[$version1])) {
			$version1 = $versiontype_lookup[$version1];
		}
		if (isset($versiontype_lookup[$version2])) {
			$version2 = $versiontype_lookup[$version2];
		}

		switch ($operator) {
			case '<':
			case 'lt':
				return intval($version1 < $version2);
				break;
			case '<=':
			case 'le':
				return intval($version1 <= $version2);
				break;
			case '>':
			case 'gt':
				return intval($version1 > $version2);
				break;
			case '>=':
			case 'ge':
				return intval($version1 >= $version2);
				break;
			case '==':
			case '=':
			case 'eq':
				return intval($version1 == $version2);
				break;
			case '!=':
			case '<>':
			case 'ne':
				return intval($version1 != $version2);
				break;
		}
		if ($version1 == $version2) {
			return 0;
		} elseif ($version1 < $version2) {
			return -1;
		}
		return 1;
	}


	function version_compare_replacement($version1, $version2, $operator='') {
		if (function_exists('version_compare')) {
			// built into PHP v4.1.0+
			return version_compare($version1, $version2, $operator);
		}

		// The function first replaces _, - and + with a dot . in the version strings
		$version1 = strtr($version1, '_-+', '...');
		$version2 = strtr($version2, '_-+', '...');

		// and also inserts dots . before and after any non number so that for example '4.3.2RC1' becomes '4.3.2.RC.1'.
		// Then it splits the results like if you were using explode('.',$ver). Then it compares the parts starting from left to right.
		$version1 = eregi_replace('([0-9]+)([A-Z]+)([0-9]+)', '\\1.\\2.\\3', $version1);
		$version2 = eregi_replace('([0-9]+)([A-Z]+)([0-9]+)', '\\1.\\2.\\3', $version2);

		$parts1 = explode('.', $version1);
		$parts2 = explode('.', $version1);
		$parts_count = max(count($parts1), count($parts2));
		for ($i = 0; $i < $parts_count; $i++) {
			$comparison = phpthumb_functions::version_compare_replacement_sub($version1, $version2, $operator);
			if ($comparison != 0) {
				return $comparison;
			}
		}
		return 0;
	}


	function phpinfo_array() {
		static $phpinfo_array = array();
		if (empty($phpinfo_array)) {
			ob_start();
			phpinfo();
			$phpinfo = ob_get_contents();
			ob_end_clean();
			$phpinfo_array = explode("\n", $phpinfo);
		}
		return $phpinfo_array;
	}


	function exif_info() {
		static $exif_info = array();
		if (empty($exif_info)) {
			// based on code by johnschaefer at gmx dot de
			// from PHP help on gd_info()
			$exif_info = array(
				'EXIF Support'           => '',
				'EXIF Version'           => '',
				'Supported EXIF Version' => '',
				'Supported filetypes'    => ''
			);
			$phpinfo_array = phpthumb_functions::phpinfo_array();
			foreach ($phpinfo_array as $dummy => $line) {
				$line = trim(strip_tags($line));
				foreach ($exif_info as $key => $value) {
					if (strpos($line, $key) === 0) {
						$newvalue = trim(str_replace($key, '', $line));
						$exif_info[$key] = $newvalue;
					}
				}
			}
		}
		return $exif_info;
	}


	function ImageTypeToMIMEtype($imagetype) {
		if (function_exists('image_type_to_mime_type') && ($imagetype >= 1) && ($imagetype <= 16)) {
			// PHP v4.3.0+
			return image_type_to_mime_type($imagetype);
		}
		static $image_type_to_mime_type = array(
			1  => 'image/gif',                     // IMAGETYPE_GIF
			2  => 'image/jpeg',                    // IMAGETYPE_JPEG
			3  => 'image/png',                     // IMAGETYPE_PNG
			4  => 'application/x-shockwave-flash', // IMAGETYPE_SWF
			5  => 'image/psd',                     // IMAGETYPE_PSD
			6  => 'image/bmp',                     // IMAGETYPE_BMP
			7  => 'image/tiff',                    // IMAGETYPE_TIFF_II (intel byte order)
			8  => 'image/tiff',                    // IMAGETYPE_TIFF_MM (motorola byte order)
			9  => 'application/octet-stream',      // IMAGETYPE_JPC
			10 => 'image/jp2',                     // IMAGETYPE_JP2
			11 => 'application/octet-stream',      // IMAGETYPE_JPX
			12 => 'application/octet-stream',      // IMAGETYPE_JB2
			13 => 'application/x-shockwave-flash', // IMAGETYPE_SWC
			14 => 'image/iff',                     // IMAGETYPE_IFF
			15 => 'image/vnd.wap.wbmp',            // IMAGETYPE_WBMP
			16 => 'image/xbm',                     // IMAGETYPE_XBM

			'gif'  => 'image/gif',                 // IMAGETYPE_GIF
			'jpg'  => 'image/jpeg',                // IMAGETYPE_JPEG
			'jpeg' => 'image/jpeg',                // IMAGETYPE_JPEG
			'png'  => 'image/png',                 // IMAGETYPE_PNG
			'bmp'  => 'image/bmp',                 // IMAGETYPE_BMP
			'ico'  => 'image/x-icon',
		);

		return (isset($image_type_to_mime_type[$imagetype]) ? $image_type_to_mime_type[$imagetype] : false);
	}


	function HexCharDisplay($string) {
		$len = strlen($string);
		$output = '';
		for ($i = 0; $i < $len; $i++) {
			$output .= ' 0x'.str_pad(dechex(ord($string{$i})), 2, '0', STR_PAD_LEFT);
		}
		return $output;
	}


	function IsHexColor($HexColorString) {
		return eregi('^[0-9A-F]{6}$', $HexColorString);
	}


	function ImageColorAllocateAlphaSafe(&$gdimg_hexcolorallocate, $R, $G, $B, $alpha=false) {
		if (phpthumb_functions::version_compare_replacement(phpversion(), '4.3.2', '>=') && ($alpha !== false)) {
			return ImageColorAllocateAlpha($gdimg_hexcolorallocate, $R, $G, $B, intval($alpha));
		} else {
			return ImageColorAllocate($gdimg_hexcolorallocate, $R, $G, $B);
		}
	}

	function ImageHexColorAllocate(&$gdimg_hexcolorallocate, $HexColorString, $dieOnInvalid=false, $alpha=false) {
		if (!is_resource($gdimg_hexcolorallocate)) {
			die('$gdimg_hexcolorallocate is not a GD resource in ImageHexColorAllocate()');
		}
		if (phpthumb_functions::IsHexColor($HexColorString)) {
			$R = hexdec(substr($HexColorString, 0, 2));
			$G = hexdec(substr($HexColorString, 2, 2));
			$B = hexdec(substr($HexColorString, 4, 2));
			return phpthumb_functions::ImageColorAllocateAlphaSafe($gdimg_hexcolorallocate, $R, $G, $B, $alpha);
		}
		if ($dieOnInvalid) {
			die('Invalid hex color string: "'.$HexColorString.'"');
		}
		return ImageColorAllocate($gdimg_hexcolorallocate, 0x00, 0x00, 0x00);
	}


	function HexColorXOR($hexcolor) {
		return strtoupper(str_pad(dechex(~hexdec($hexcolor) & 0xFFFFFF), 6, '0', STR_PAD_LEFT));
	}


	function GetPixelColor(&$img, $x, $y) {
		if (!is_resource($img)) {
			return false;
		}
		return @ImageColorsForIndex($img, @ImageColorAt($img, $x, $y));
	}


	function GrayscaleValue($r, $g, $b) {
		return round(($r * 0.30) + ($g * 0.59) + ($b * 0.11));
	}


	function GrayscalePixel($OriginalPixel) {
		$gray = phpthumb_functions::GrayscaleValue($OriginalPixel['red'], $OriginalPixel['green'], $OriginalPixel['blue']);
		return array('red'=>$gray, 'green'=>$gray, 'blue'=>$gray);
	}


	function GrayscalePixelRGB($rgb) {
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >>  8) & 0xFF;
		$b =  $rgb        & 0xFF;
		return ($r * 0.299) + ($g * 0.587) + ($b * 0.114);
	}


	function ImageCopyResampleBicubic($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {
		// ron at korving dot demon dot nl
		// http://www.php.net/imagecopyresampled

		$scaleX = ($src_w - 1) / $dst_w;
		$scaleY = ($src_h - 1) / $dst_h;

		$scaleX2 = $scaleX / 2.0;
		$scaleY2 = $scaleY / 2.0;

		$isTrueColor = ImageIsTrueColor($src_img);

		for ($y = $src_y; $y < $src_y + $dst_h; $y++) {
			$sY   = $y * $scaleY;
			$siY  = (int) $sY;
			$siY2 = (int) $sY + $scaleY2;

			for ($x = $src_x; $x < $src_x + $dst_w; $x++) {
				$sX   = $x * $scaleX;
				$siX  = (int) $sX;
				$siX2 = (int) $sX + $scaleX2;

				if ($isTrueColor) {

					$c1 = ImageColorAt($src_img, $siX, $siY2);
					$c2 = ImageColorAt($src_img, $siX, $siY);
					$c3 = ImageColorAt($src_img, $siX2, $siY2);
					$c4 = ImageColorAt($src_img, $siX2, $siY);

					$r = (( $c1             +  $c2             +  $c3             +  $c4            ) >> 2) & 0xFF0000;
					$g = ((($c1 & 0x00FF00) + ($c2 & 0x00FF00) + ($c3 & 0x00FF00) + ($c4 & 0x00FF00)) >> 2) & 0x00FF00;
					$b = ((($c1 & 0x0000FF) + ($c2 & 0x0000FF) + ($c3 & 0x0000FF) + ($c4 & 0x0000FF)) >> 2);

				} else {

					$c1 = ImageColorsForIndex($src_img, ImageColorAt($src_img, $siX, $siY2));
					$c2 = ImageColorsForIndex($src_img, ImageColorAt($src_img, $siX, $siY));
					$c3 = ImageColorsForIndex($src_img, ImageColorAt($src_img, $siX2, $siY2));
					$c4 = ImageColorsForIndex($src_img, ImageColorAt($src_img, $siX2, $siY));

					$r = ($c1['red']   + $c2['red']   + $c3['red']   + $c4['red'] )  << 14;
					$g = ($c1['green'] + $c2['green'] + $c3['green'] + $c4['green']) <<  6;
					$b = ($c1['blue']  + $c2['blue']  + $c3['blue']  + $c4['blue'] ) >>  2;

				}
				ImageSetPixel($dst_img, $dst_x + $x - $src_x, $dst_y + $y - $src_y, $r+$g+$b);
			}
		}
		return true;
	}


	function ImageCreateFunction($x_size, $y_size) {
		$ImageCreateFunction = 'ImageCreate';
		if (phpthumb_functions::gd_version() >= 2.0) {
			$ImageCreateFunction = 'ImageCreateTrueColor';
		}
		if (!function_exists($ImageCreateFunction)) {
			return phpthumb::ErrorImage($ImageCreateFunction.'() does not exist - no GD support?');
		}
		if (($x_size <= 0) || ($y_size <= 0)) {
			return phpthumb::ErrorImage('Invalid image dimensions: '.$ImageCreateFunction.'('.$x_size.', '.$y_size.')');
		}
		return $ImageCreateFunction($x_size, $y_size);
	}


	function ImageCopyRespectAlpha(&$dst_im, &$src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct=100) {
		for ($x = $src_x; $x < $src_w; $x++) {
			for ($y = $src_y; $y < $src_h; $y++) {
				$RealPixel    = phpthumb_functions::GetPixelColor($dst_im, $dst_x + $x, $dst_y + $y);
				$OverlayPixel = phpthumb_functions::GetPixelColor($src_im, $x, $y);
				$alphapct = $OverlayPixel['alpha'] / 127;
				$opacipct = $pct / 100;
				$overlaypct = (1 - $alphapct) * $opacipct;

				$newcolor = phpthumb_functions::ImageColorAllocateAlphaSafe(
					$dst_im,
					round($RealPixel['red']   * (1 - $overlaypct)) + ($OverlayPixel['red']   * $overlaypct),
					round($RealPixel['green'] * (1 - $overlaypct)) + ($OverlayPixel['green'] * $overlaypct),
					round($RealPixel['blue']  * (1 - $overlaypct)) + ($OverlayPixel['blue']  * $overlaypct),
					//$RealPixel['alpha']);
					0);

				ImageSetPixel($dst_im, $dst_x + $x, $dst_y + $y, $newcolor);
			}
		}
		return true;
	}


	function ProportionalResize($old_width, $old_height, $new_width=false, $new_height=false) {
		$old_aspect_ratio = $old_width / $old_height;
		if (($new_width === false) && ($new_height === false)) {
			return false;
		} elseif ($new_width === false) {
			$new_width = $new_height * $old_aspect_ratio;
		} elseif ($new_height === false) {
			$new_height = $new_width / $old_aspect_ratio;
		}
		$new_aspect_ratio = $new_width / $new_height;
		if ($new_aspect_ratio == $old_aspect_ratio) {
			// great, done
		} elseif ($new_aspect_ratio < $old_aspect_ratio) {
			// limited by width
			$new_height = $new_width / $old_aspect_ratio;
		} elseif ($new_aspect_ratio > $old_aspect_ratio) {
			// limited by height
			$new_width = $new_height * $old_aspect_ratio;
		}
		return array(round($new_width), round($new_height));
	}


	function FunctionIsDisabled($function) {
		static $DisabledFunctions = null;
		if (is_null($DisabledFunctions)) {
			$disable_functions_local  = explode(',',     @ini_get('disable_functions'));
			$disable_functions_global = explode(',', @get_cfg_var('disable_functions'));
			foreach ($disable_functions_local as $key => $value) {
				$DisabledFunctions[$value] = 'local';
			}
			foreach ($disable_functions_global as $key => $value) {
				$DisabledFunctions[$value] = 'global';
			}
			if (@ini_get('safe_mode')) {
				$DisabledFunctions['shell_exec'] = 'local';
			}
		}
		return isset($DisabledFunctions[$function]);
	}


	function SafeExec($command) {
		static $AllowedExecFunctions = array();
		if (empty($AllowedExecFunctions)) {
			$AllowedExecFunctions = array('shell_exec'=>true, 'passthru'=>true, 'system'=>true, 'exec'=>true);
			foreach ($AllowedExecFunctions as $key => $value) {
				//$AllowedExecFunctions[$key] = !phpthumb_functions::FunctionIsDisabled($key);
			}
		}
		foreach ($AllowedExecFunctions as $execfunction => $is_allowed) {
			if (!$is_allowed) {
				continue;
			}
			switch ($execfunction) {
				case 'passthru':
					ob_start();
					$execfunction($command);
					$returnvalue = ob_get_contents();
					ob_end_clean();
					break;

				case 'shell_exec':
				case 'system':
				case 'exec':
				default:
					ob_start();
					$returnvalue = $execfunction($command);
					ob_end_clean();
					break;
			}
			return $returnvalue;
		}
		return false;
	}


	function ApacheLookupURIarray($filename) {
		// apache_lookup_uri() only works when PHP is installed as an Apache module.
		if (php_sapi_name() == 'apache') {
			$keys = array('status', 'the_request', 'status_line', 'method', 'content_type', 'handler', 'uri', 'filename', 'path_info', 'args', 'boundary', 'no_cache', 'no_local_copy', 'allowed', 'send_bodyct', 'bytes_sent', 'byterange', 'clength', 'unparsed_uri', 'mtime', 'request_time');
			if ($apacheLookupURIobject = @apache_lookup_uri($filename)) {
				$apacheLookupURIarray = array();
				foreach ($keys as $dummy => $key) {
					$apacheLookupURIarray[$key] = @$apacheLookupURIobject->$key;
				}
				return $apacheLookupURIarray;
			}
		}
		return false;
	}


	function gd_is_bundled() {
		static $isbundled = null;
		if (is_null($isbundled)) {
			$gd_info = gd_info();
			$isbundled = (strpos($gd_info['GD Version'], 'bundled') !== false);
		}
		return $isbundled;
	}


	function gd_version($fullstring=false) {
		static $cache_gd_version = array();
		if (empty($cache_gd_version)) {
			$gd_info = gd_info();
			if (eregi('bundled \((.+)\)$', $gd_info['GD Version'], $matches)) {
				$cache_gd_version[1] = $gd_info['GD Version'];  // e.g. "bundled (2.0.15 compatible)"
				$cache_gd_version[0] = (float) $matches[1];     // e.g. "2.0" (not "bundled (2.0.15 compatible)")
			} else {
				$cache_gd_version[1] = $gd_info['GD Version'];                       // e.g. "1.6.2 or higher"
				$cache_gd_version[0] = (float) substr($gd_info['GD Version'], 0, 3); // e.g. "1.6" (not "1.6.2 or higher")
			}
		}
		return $cache_gd_version[intval($fullstring)];
	}


	function filesize_remote($remotefile, $timeout=10) {
		$size = false;
		$url = parse_url($remotefile);
		if ($fp = @fsockopen($url['host'], ($url['port'] ? $url['port'] : 80), $errno, $errstr, $timeout)) {
			fwrite($fp, 'HEAD '.@$url['path'].@$url['query'].' HTTP/1.0'."\r\n".'Host: '.@$url['host']."\r\n\r\n");
			if (phpthumb_functions::version_compare_replacement(phpversion(), '4.3.0', '>=')) {
				stream_set_timeout($fp, $timeout);
			}
			while (!feof($fp)) {
				$headerline = fgets($fp, 4096);
				if (eregi('^Content-Length: (.*)', $headerline, $matches)) {
					$size = intval($matches[1]);
					break;
				}
			}
			fclose ($fp);
		}
		return $size;
	}


	function filedate_remote($remotefile, $timeout=10) {
		$date = false;
		$url = parse_url($remotefile);
		if ($fp = @fsockopen($url['host'], ($url['port'] ? $url['port'] : 80), $errno, $errstr, $timeout)) {
			fwrite($fp, 'HEAD '.@$url['path'].@$url['query'].' HTTP/1.0'."\r\n".'Host: '.@$url['host']."\r\n\r\n");
			if (phpthumb_functions::version_compare_replacement(phpversion(), '4.3.0', '>=')) {
				stream_set_timeout($fp, $timeout);
			}
			while (!feof($fp)) {
				$headerline = fgets($fp, 4096);
				if (eregi('^Last-Modified: (.*)', $headerline, $matches)) {
					$date = strtotime($matches[1]) - date('Z');
					break;
				}
			}
			fclose ($fp);
		}
		return $date;
	}


	function md5_file_safe($filename) {
		// md5_file() doesn't exist in PHP < 4.2.0
		if (function_exists('md5_file')) {
			return md5_file($filename);
		}
		if ($fp = @fopen($filename, 'rb')) {
			$rawData = '';
			do {
				$buffer = fread($fp, 8192);
				$rawData .= $buffer;
			} while (strlen($buffer) > 0);
			fclose($fp);
			return md5($rawData);
		}
		return false;
	}


	function nonempty_min() {
		$arg_list = func_get_args();
		$acceptable = array();
		foreach ($arg_list as $dummy => $arg) {
			if ($arg) {
				$acceptable[] = $arg;
			}
		}
		return min($acceptable);
	}


	function LittleEndian2String($number, $minbytes=1) {
		$intstring = '';
		while ($number > 0) {
			$intstring = $intstring.chr($number & 255);
			$number >>= 8;
		}
		return str_pad($intstring, $minbytes, "\x00", STR_PAD_RIGHT);
	}

	function OneOfThese() {
		// return the first useful (non-empty/non-zero/non-false) value from those passed
		$arg_list = func_get_args();
		foreach ($arg_list as $key => $value) {
			if ($value) {
				return $value;
			}
		}
		return false;
	}

	function CaseInsensitiveInArray($needle, $haystack) {
		$needle = strtolower($needle);
		foreach ($haystack as $key => $value) {
			if (is_array($value)) {
				// skip?
			} elseif ($needle == strtolower($value)) {
				return true;
			}
		}
		return false;
	}

	function URLreadFsock($host, $file, &$errstr, $successonly=true, $port=80, $timeout=10) {
		if (!function_exists('fsockopen') || phpthumb_functions::FunctionIsDisabled('fsockopen')) {
			$errstr = 'fsockopen() unavailable';
			return false;
		}
		if ($fp = @fsockopen($host, 80, $errno, $errstr, 15)) {
			$out  = 'GET '.$file.' HTTP/1.0'."\r\n";
			$out .= 'Host: '.$host."\r\n";
			$out .= 'Connection: Close'."\r\n\r\n";
			fwrite($fp, $out);

			$isHeader = true;
			$Data_header = '';
			$Data_body   = '';
			$header_newlocation = '';
			while (!feof($fp)) {
				$line = fgets($fp, 1024);
				if ($isHeader) {
					$Data_header .= $line;
				} else {
					$Data_body .= $line;
				}
				if (eregi('^HTTP/[\\.0-9]+ ([0-9]+) (.+)$', rtrim($line), $matches)) {
					list($dummy, $errno, $errstr) = $matches;
					$errno = intval($errno);
				} elseif (eregi('^Location: (.*)$', rtrim($line), $matches)) {
					$header_newlocation = $matches[1];
				}
				if ($isHeader && ($line == "\r\n")) {
					$isHeader = false;
					if ($successonly) {
						if ($errno == 200) {
							// great, continue
						} else {
							$errstr = $errno.' '.$errstr.($header_newlocation ? '; Location: '.$header_newlocation : '');
							fclose($fp);
							return false;
						}
					}
				}
			}
			fclose($fp);
			return $Data_body;
		}
		return null;
	}

	function SafeURLread($url, &$error) {
		$error = '';

		$parsed_url = @parse_url($url);
		$rawData = phpthumb_functions::URLreadFsock(@$parsed_url['host'], @$parsed_url['path'], $errstr, true, (@$parsed_url['port'] ? @$parsed_url['port'] : 80));
		$error .= 'Error: '.$errstr."\n".$url;
		if ($rawData === false) {
			return false;
		} elseif ($rawData === null) {
			// fall through
		} else {
			return $rawData;
		}

		$BrokenURLfopenPHPversions = array('4.4.2');
		if (in_array(phpversion(), $BrokenURLfopenPHPversions)) {
			$error .= 'fopen(URL) broken in PHP v'.phpversion().'; ';
		} elseif (@ini_get('allow_url_fopen')) {
			$rawData = '';
			ob_start();
			if ($fp = fopen($url, 'rb')) {
				do {
					$buffer = fread($fp, 8192);
					$rawData .= $buffer;
				} while (strlen($buffer) > 0);
				fclose($fp);
			} else {
				$error .= trim(strip_tags(ob_get_contents()));
			}
			ob_end_clean();
			if (!$error) {
				return $rawData;
			}
			$error .= '; "allow_url_fopen" enabled but returned no data; ';
		} else {
			$error .= '"allow_url_fopen" disabled; ';
		}

		if (function_exists('curl_version') && !phpthumb_functions::FunctionIsDisabled('curl_exec')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
			$rawData = curl_exec($ch);
			curl_close($ch);
			if (strlen($rawData) > 0) {
				return $rawData;
			}
			$error .= 'CURL available but returned no data; ';
		} else {
			$error .= 'CURL unavailable; ';
		}
		return false;
	}

}


if (!function_exists('gd_info')) {
	// built into PHP v4.3.0+ (with bundled GD2 library)
	function gd_info() {
		static $gd_info = array();
		if (empty($gd_info)) {
			// based on code by johnschaefer at gmx dot de
			// from PHP help on gd_info()
			$gd_info = array(
				'GD Version'         => '',
				'FreeType Support'   => false,
				'FreeType Linkage'   => '',
				'T1Lib Support'      => false,
				'GIF Read Support'   => false,
				'GIF Create Support' => false,
				'JPG Support'        => false,
				'PNG Support'        => false,
				'WBMP Support'       => false,
				'XBM Support'        => false
			);
			$phpinfo_array = phpthumb_functions::phpinfo_array();
			foreach ($phpinfo_array as $dummy => $line) {
				$line = trim(strip_tags($line));
				foreach ($gd_info as $key => $value) {
					//if (strpos($line, $key) !== false) {
					if (strpos($line, $key) === 0) {
						$newvalue = trim(str_replace($key, '', $line));
						$gd_info[$key] = $newvalue;
					}
				}
			}
			if (empty($gd_info['GD Version'])) {
				// probable cause: "phpinfo() disabled for security reasons"
				if (function_exists('ImageTypes')) {
					$imagetypes = ImageTypes();
					if ($imagetypes & IMG_PNG) {
						$gd_info['PNG Support'] = true;
					}
					if ($imagetypes & IMG_GIF) {
						$gd_info['GIF Create Support'] = true;
					}
					if ($imagetypes & IMG_JPG) {
						$gd_info['JPG Support'] = true;
					}
					if ($imagetypes & IMG_WBMP) {
						$gd_info['WBMP Support'] = true;
					}
				}
				// to determine capability of GIF creation, try to use ImageCreateFromGIF on a 1px GIF
				if (function_exists('ImageCreateFromGIF')) {
					if ($tempfilename = phpthumb::phpThumb_tempnam()) {
						if ($fp_tempfile = @fopen($tempfilename, 'wb')) {
							fwrite($fp_tempfile, base64_decode('R0lGODlhAQABAIAAAH//AP///ywAAAAAAQABAAACAUQAOw==')); // very simple 1px GIF file base64-encoded as string
							fclose($fp_tempfile);

							// if we can convert the GIF file to a GD image then GIF create support must be enabled, otherwise it's not
							$gd_info['GIF Read Support'] = (bool) @ImageCreateFromGIF($tempfilename);
						}
						unlink($tempfilename);
					}
				}
				if (function_exists('ImageCreateTrueColor') && @ImageCreateTrueColor(1, 1)) {
					$gd_info['GD Version'] = '2.0.1 or higher (assumed)';
				} elseif (function_exists('ImageCreate') && @ImageCreate(1, 1)) {
					$gd_info['GD Version'] = '1.6.0 or higher (assumed)';
				}
			}
		}
		return $gd_info;
	}
}


if (!function_exists('is_executable')) {
	// in PHP v3+, but v5.0+ for Windows
	function is_executable($filename) {
		// poor substitute, but better than nothing
		return file_exists($filename);
	}
}


if (!function_exists('preg_quote')) {
	// included in PHP v3.0.9+, but may be unavailable if not compiled in
	function preg_quote($string, $delimiter='\\') {
		static $preg_quote_array = array();
		if (empty($preg_quote_array)) {
			$escapeables = '.\\+*?[^]$(){}=!<>|:';
			for ($i = 0; $i < strlen($escapeables); $i++) {
				$strtr_preg_quote[$escapeables{$i}] = $delimiter.$escapeables{$i};
			}
		}
		return strtr($string, $strtr_preg_quote);
	}
}

if (!function_exists('file_get_contents')) {
	// included in PHP v4.3.0+
	function file_get_contents($filename) {
		if (eregi('^(f|ht)tp\://', $filename)) {
			return SafeURLread($filename);
		}
		if ($fp = @fopen($filename, 'rb')) {
			$rawData = '';
			do {
				$buffer = fread($fp, 8192);
				$rawData .= $buffer;
			} while (strlen($buffer) > 0);
			fclose($fp);
			return $rawData;
		}
		return false;
	}
}


if (!function_exists('file_put_contents')) {
	// included in PHP v5.0.0+
	function file_put_contents($filename, $filedata) {
		if ($fp = @fopen($filename, 'wb')) {
			fwrite($fp, $filedata);
			fclose($fp);
			return true;
		}
		return false;
	}
}

if (!function_exists('imagealphablending')) {
	// built-in function requires PHP v4.0.6+ *and* GD v2.0.1+
	function imagealphablending(&$img, $blendmode=true) {
		// do nothing, this function is declared here just to
		// prevent runtime errors if GD2 is not available
		return true;
	}
}

if (!function_exists('imagesavealpha')) {
	// built-in function requires PHP v4.3.2+ *and* GD v2.0.1+
	function imagesavealpha(&$img, $blendmode=true) {
		// do nothing, this function is declared here just to
		// prevent runtime errors if GD2 is not available
		return true;
	}
}

?>