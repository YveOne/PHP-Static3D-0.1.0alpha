<?php

/**
* Static3D miscellaneous functions
*/
class S3D_Misc
{
    private function __construct(){}

    static private $_flagsTopology = [S3D_POINTS, S3D_LINELIST, S3D_LINESTRIP, S3D_TRIANGLELIST, S3D_TRIANGLESTRIP];
    static private $_flagsSkin = [S3D_COLORED, S3D_TEXTURED];

    static public function flagsIsset($value, $flag)
    {
        return (($value & $flag) == $flag);
    }

    static public function flagsGetTopology($value)
    {
        foreach(self::$_flagsTopology as &$flag)
            if(($value & $flag) == $flag) return $flag;
        return 0;
    }

    static public function flagsGetSkin($value)
    {
        foreach(self::$_flagsSkin as &$flag)
            if(($value & $flag) == $flag) return $flag;
        return 0;
    }

}
