<?php
class captcha
{

	public static function destroy()
	{
		$_SESSION["captcha"] = '';
	}

	public static function createAuthcode()
	{
		header('Cache-Control: no-cache, must-revalidate');
		$array = "34679ACDEFGHJKLMNPRTUVWXY";
		$authcode = "";
		for($i = 0; $i < 6; $i++)
		{
			$authcode .= substr($array, rand(0, 24), 1);
		}
		$_SESSION["captcha"] = $authcode;

		$imgWidth = 100;
		$imgHeight = 20;
		$imgFont = 8;
		self::doOutputImg($authcode, $imgWidth, $imgHeight, $imgFont);
	}

	//输出校验码图像
	public static function doOutputImg($string, $imgWidth, $imgHeight, $imgFont, $imgFgColorArr = array(0,0,0), $imgBgColorArr = array(255,255,255))
	{
		$image = imagecreatetruecolor($imgWidth, $imgHeight);

		//用白色背景加黑色边框画个方框
		$backColor = imagecolorallocate($image, 255, 255, 255);
		$borderColor = imagecolorallocate($image, 0, 0, 0);
		imagefilledrectangle($image, 0, 0, $imgWidth - 1, $imgHeight - 1, $backColor);
		imagerectangle($image, 0, 0, $imgWidth - 1, $imgHeight - 1, $borderColor);

		$imgFgColor = imagecolorallocate($image, $imgFgColorArr[0], $imgFgColorArr[1], $imgFgColorArr[2]);
		self::doDrawStr($image, $string, $imgFgColor, $imgFont);
		self::doPollute($image, 64);

		header('Content-type: image/png');
		imagepng($image);
		imagedestroy($image);
	}

	//画出校验码
	public static function doDrawStr($image, $string, $color, $imgFont)
	{
		$imgWidth = imagesx($image);
		$imgHeight = imagesy($image);

		$count = strlen($string);
		$xpace = ($imgWidth / $count);

		$x = ($xpace - 6) / 2;
		$y = ($imgHeight / 2 - 8);
		for($p = 0; $p < $count; $p++)
		{
			$xoff = rand(-2, +2);
			$yoff = rand(-2, +2);
			$curChar = substr($string, $p, 1);
			imagestring($image, $imgFont, $x + $xoff, $y + $yoff, $curChar, $color);
			$x += $xpace;
		}

		return 0;
	}

	//画出一些杂点
	public static function doPollute($image, $times)
	{
		$imgWidth = imagesx($image);
		$imgHeight = imagesy($image);
		for($j = 0; $j < $times; $j++)
		{
			$x = rand(0, $imgWidth);
			$y = rand(0, $imgHeight);

			$color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
			imagesetpixel($image, $x, $y, $color);
		}
	}
}
