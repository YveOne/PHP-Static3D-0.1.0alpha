<?php

/**
* Static3D zBuffer class
*
* simple zbuffer system that uses a bitmap and integers
*/
class S3D_zBuffer
{
    private function __construct(){}

    // image resource
    static private $_res = null;

    /**
    * init() returns an anonymous function
    * for checking and setting zbuffer directly
    */
    static public function init($width, $height, $color)
    {
        $_res = @imagecreatetruecolor($width, $height);
        imagefill($_res, 0, 0, $color);
        self::$_res = &$_res;
        return function($x, $y, $z) use($_res)
        {
            if($z >= imagecolorat($_res, $x, $y)) return false;
            imagesetpixel($_res, $x, $y, $z);
            return true;
        };
    }

    // destroy image
    static public function dispose()
    {
        imagedestroy(self::$_res);
        self::$_res = null;
    }

}
