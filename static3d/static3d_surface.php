<?php

/**
* Static3D Surface class
*/
class S3D_Surface
{
    private function __construct(){}

    // image resource
    static private $_res = null;

    /**
    * init() returns an anonymous function
    * for setting pixels directly
    */
    static public function init($res)
    {
        if(!$res) return false;
        self::$_res = &$res;
        return function($x, $y, $c) use($res)
        {
            imagesetpixel($res, $x, $y, $c);
        };
    }

    static public function output($type, $quality, $file)
    {
        switch($type)
        {
            case S3D_PNG:
                if($quality===null) $quality = 9;
                if(!$file)
                {
                    header('Content-Type: image/png');
                    header('Content-Disposition: inline; filename="'.S3D_FILENAME.'.png"');
                }
                imagepng(self::$_res, $file, $quality);
                break;
            case S3D_JPG:
                if($quality===null) $quality = 75;
                if(!$file)
                {
                    header('Content-Type: image/jpeg');
                    header('Content-Disposition: inline; filename="'.S3D_FILENAME.'.jpg"');
                }
                imagejpeg(self::$_res, $file, $quality);
                break;
        }
    }

    // size
    static public function width()
    {
        return imagesx(self::$_res);
    }
    static public function height()
    {
        return imagesy(self::$_res);
    }

    // destroy image
    static public function dispose()
    {
        imagedestroy(self::$_res);
        self::$_res = null;
    }


    static private $_systemStringY = S3D_DEBUG_POSITIONY;
    static public function drawSystemString($text="")
    {

            imagestring(self::$_res, S3D_DEBUG_SYSTEMFONT, S3D_DEBUG_POSITIONX+1, self::$_systemStringY+1, $text, S3D_DEBUG_BACKCOLOR);
            imagestring(self::$_res, S3D_DEBUG_SYSTEMFONT, S3D_DEBUG_POSITIONX-1, self::$_systemStringY-1, $text, S3D_DEBUG_BACKCOLOR);
            imagestring(self::$_res, S3D_DEBUG_SYSTEMFONT, S3D_DEBUG_POSITIONX-1, self::$_systemStringY+1, $text, S3D_DEBUG_BACKCOLOR);
            imagestring(self::$_res, S3D_DEBUG_SYSTEMFONT, S3D_DEBUG_POSITIONX+1, self::$_systemStringY-1, $text, S3D_DEBUG_BACKCOLOR);

            imagestring(self::$_res, S3D_DEBUG_SYSTEMFONT, S3D_DEBUG_POSITIONX+1, self::$_systemStringY, $text, S3D_DEBUG_BACKCOLOR);
            imagestring(self::$_res, S3D_DEBUG_SYSTEMFONT, S3D_DEBUG_POSITIONX-1, self::$_systemStringY, $text, S3D_DEBUG_BACKCOLOR);
            imagestring(self::$_res, S3D_DEBUG_SYSTEMFONT, S3D_DEBUG_POSITIONX, self::$_systemStringY+1, $text, S3D_DEBUG_BACKCOLOR);
            imagestring(self::$_res, S3D_DEBUG_SYSTEMFONT, S3D_DEBUG_POSITIONX, self::$_systemStringY-1, $text, S3D_DEBUG_BACKCOLOR);

        imagestring(self::$_res, S3D_DEBUG_SYSTEMFONT, S3D_DEBUG_POSITIONX, self::$_systemStringY, $text, S3D_DEBUG_FRONTCOLOR);
        self::$_systemStringY += S3D_DEBUG_FONTHEIGHT;
    }

    static public function overlay($that, $x, $y, $anchor=S3D_ANCHOR_TL)
    {
        $thisW = imagesx(self::$_res);
        $thisH = imagesy(self::$_res);
        $thatW = imagesx($that);
        $thatH = imagesy($that);
        switch($anchor)
        {
            case S3D_ANCHOR_TL:
                $x = $x;
                $y = $y;
                break;
            case S3D_ANCHOR_TR:
                $x = $thisW - $thatW + $x;
            case S3D_ANCHOR_BL:
                $y = $thisH - $thatH + $y;
            case S3D_ANCHOR_BR:
                $x = $thisW - $thatW + $x;
                $y = $thisH - $thatH + $y;
                break;
        }
        imagecopy(self::$_res, $that, $x, $y, 0, 0, $thatW, $thatH);
    }

}
