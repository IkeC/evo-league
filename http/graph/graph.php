<?php

function getGraph($grdata, $height, $width, $imageurl) {
	return getFullGraph($grdata, $height, $width, $imageurl, null, null);
}

function getFullGraph($grdata, $height, $width, $imageurl, $winStreak, $loseStreak) {
	require('./../variables.php');	
	// BackGround color
	if (!empty ($_GET["bgc"])) {
		$bgcolor["r"] = hexdec(substr($_GET["bgc"], 0, 2));
		$bgcolor["g"] = hexdec(substr($_GET["bgc"], 2, 2));
		$bgcolor["b"] = hexdec(substr($_GET["bgc"], 4, 2));
	} else {
		$bgcolor = array ("r" => 243, "g" => 243, "b" => 255); //DEFAULT BG Color
	}
	// Line color
	$fcolor = array ("r" => 0, "g" => 0, "b" => 0); // DEFAULT Line Color

	// Image Height
	if (!empty ($height)) {
		$img_height = $height;
	} else {
		$img_height = 30; // DEFAULT
	}

	if ($grdata == null) {
		$img_width = $width;
		if ($imageurl == null) {
			$img = imagecreate($img_width, $img_height); //CREATE image
			$img_bgc = imagecolorallocate($img, $bgcolor["r"], $bgcolor["g"], $bgcolor["b"]);
			imagefilledrectangle($img, 0, 0, $img_width, $img_height, $img_bgc); // Draw background color over image
		}
		else {
			$img = imagecreatefrompng($imageurl);
		}
		$font_line1 = 3;
		$offset_left = $img_width - 70;
		$offset_line1 = $img_height - 20;
		$string_line1 = "no data";

		$fontcolor_normal = imagecolorallocate($img, 0, 0, 0);

		imagestring($img, $font_line1, $offset_left, $offset_line1, $string_line1, $fontcolor_normal);
		return $img;
	}
	
	if (!empty ($width)) {
		$img_width = $width;
	} else {
		$img_width = round($img_height) * (sizeof($grdata)); // DEFAULT
	}
	
	$itemVal = 0;
	$data_max = 0;
	$data_min = 0;
	
	$stepData = array();
	foreach ($grdata as $item) {
		$itemVal += $item[0];
		$stepData[] = $itemVal;
		$dbgStr .= "<br> item:".$item[0]." val:".$itemVal."<br>";
		if ($itemVal > $data_max) {
			$data_max = $itemVal;
		} else
			if ($itemVal < $data_min) {
				$data_min = $itemVal;
			}
	}
	if ($data_max == $data_min) {
		// MAX and MIN is same => CENTER THE LINE
		$data_max = $data_max +1;
		$data_min = $data_min -1;
	}
	$data_const = $img_height / ($data_max - $data_min); //ReCalculating CONST (used to center graph (y-array)
	$dbgStr .= "data_max: ".$data_max." data_min: ".$data_min." data_const: ".$data_const;
	if (sizeof($grdata) > 1) {
		$data_step = $img_width / (sizeof($grdata) - 1); // STEP for drawing (x-array)
	} else {
		$data_step = $img_width;
	}
	if (!empty ($stepData)) {
		foreach ($stepData as $value) {
			// ReCalculate data array to fit the image
			$res = round(($value - $data_min) * $data_const, 3);
			$res = $img_height - $res;
			if ($res < 0)
				$res ++;
			if ($res >= $img_height)
				$res --;
			$data_gr[] = $res;
		}
	}
	
	if ($imageurl == null) {
		$img = imagecreate($img_width, $img_height); //CREATE image
		$img_bgc = imagecolorallocate($img, $bgcolor["r"], $bgcolor["g"], $bgcolor["b"]);
		$img_fc = imagecolorallocate($img, $fcolor["r"], $fcolor["g"], $fcolor["b"]);
		imagefilledrectangle($img, 0, 0, $img_width, $img_height, $img_bgc); // Draw background color over image
	}
	else {
		$img = imagecreatefrompng($imageurl);
	}
	$x = 0; // draw helper
	$y = $data_gr[0]; // DATA helper (first value)
	$dbgStr .= "<br>grdata: ".print_r($grdata, true)."<br>";
	$dbgStr .= "<br>data_gr: ".print_r($data_gr, true)."<br>";
	
	for ($loop = 1; $loop < sizeof($grdata); $loop ++) {
		// LineDrawing LOOP
		$x_start = $x;
		$y_start = $y;
		$dbgStr .= "------------------------------ grdata: <b>".$grdata[$loop]."</b> - data_gr: <b>".$data_gr[$loop]."</b><Br>";
		$dbgStr .= "x_start: <b>".$x_start."</b> - y_start: <b>".$y_start."</b><br>";
		$x = $x + $data_step;
		$y = $data_gr[$loop];
		$colorHex = $grdata[$loop-1][1];
		 
		$color = imagecolorallocate($img, 
			hexdec(substr($colorHex, 0, 2)),
			hexdec(substr($colorHex, 2, 2)),
			hexdec(substr($colorHex, 4, 2)));
			 
		$dbgStr .= "x: <b>".$x."</b> - y: <b>".$y."</b><p>";
	
		if (strcmp($colorHex, $graph_red) == 0 || strcmp($colorHex, $graph_green) == 0) {
			$thickness = 1.5;
		} else {
			$thickness = 1;
		}
		imagelinethick($img, $x_start, $y_start, $x, $y, $color, $thickness);
		$streak = $grdata[$loop-1][2];
		if ($streak > 0) {
			if (strcmp($colorHex, $graph_red) == 0) {
				$offx = 0;
				$offy = -13;
			} else {
				$offx = -10;
				$offy = -13;
			}
			$font = 1;
			$fontcolor = $color;
			$offset_left = $x + $offx;
			$offset_top = $y + $offy;
			
			if ($offset_top < 15) {
				$offset_top = $offset_top + 20; 
			}
			imagestring($img, $font, $offset_left, $offset_top, $streak, $fontcolor);
		}
	}
	return $img;
}

function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
{
   /* this way it works well only for orthogonal lines
   imagesetthickness($image, $thick);
   return imageline($image, $x1, $y1, $x2, $y2, $color);
   */
   if ($thick == 1) {
       return imageline($image, $x1, $y1, $x2, $y2, $color);
   }
   $t = $thick / 2 - 0.5;
   if ($x1 == $x2 || $y1 == $y2) {
       return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
   }
   $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
   $a = $t / sqrt(1 + pow($k, 2));
   $points = array(
       round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
       round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
       round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
       round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
   );   
   imagefilledpolygon($image, $points, 4, $color);
   return imagepolygon($image, $points, 4, $color);
}

?>