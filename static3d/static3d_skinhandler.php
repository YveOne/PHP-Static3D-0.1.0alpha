<?php

//TODO: docu

class S3D_SkinHandler
{

    static private $_texture = null;

    static function setTexture(&$res)
    {
        self::$_texture = &$res;
    }

    // destroy image
    static public function dispose()
    {
        imagedestroy(self::$_texture);
        self::$_texture = null;
    }

/////////////////////////////////////////////////
//REGION Colored

    static function initColoredPoint(&$skin1)
    {
        $pColor = &$skin1[0];
        $pR = ($pColor >> 16) & 0xFF;
        $pG = ($pColor >>  8) & 0xFF;
        $pB =  $pColor        & 0xFF;
        return function() use(&$pR, &$pG, &$pB)
        {
            return ($pR << 16) + ($pG << 8) + ($pB);
        };
    }

    static function initColoredLine(&$skin1, &$skin2)
    {
        $fromColor = &$skin1[0];
        $fromR = ($fromColor >> 16) & 0xFF;
        $fromG = ($fromColor >>  8) & 0xFF;
        $fromB =  $fromColor        & 0xFF;
        $toColor = &$skin2[0];
        $toR = ($toColor >> 16) & 0xFF;
        $toG = ($toColor >>  8) & 0xFF;
        $toB =  $toColor        & 0xFF;
        return function(&$w1, &$w2) use(&$fromR, &$fromG, &$fromB, &$toR, &$toG, &$toB)
        {
            $r = (int)($fromR*$w1 + $toR*$w2);
            $g = (int)($fromG*$w1 + $toG*$w2);
            $b = (int)($fromB*$w1 + $toB*$w2);
            return ($r << 16) + ($g << 8) + ($b);
        };
    }

    static function initColoredTriangle(&$skin1, &$skin2, &$skin3)
    {
        $skin1color = &$skin1[0];
        $skin1r = ($skin1color >> 16) & 0xFF;
        $skin1g = ($skin1color >>  8) & 0xFF;
        $skin1b =  $skin1color        & 0xFF;
        $skin2color = &$skin2[0];
        $skin2r = ($skin2color >> 16) & 0xFF;
        $skin2g = ($skin2color >>  8) & 0xFF;
        $skin2b =  $skin2color        & 0xFF;
        $skin3color = &$skin3[0];
        $skin3r = ($skin3color >> 16) & 0xFF;
        $skin3g = ($skin3color >>  8) & 0xFF;
        $skin3b =  $skin3color        & 0xFF;
        return function(&$w1, &$w2, &$w3) use(&$skin1r, &$skin1g, &$skin1b, &$skin2r, &$skin2g, &$skin2b, &$skin3r, &$skin3g, &$skin3b)
        {
            $r = (int)($skin1r*$w1 + $skin2r*$w2 + $skin3r*$w3);
            $g = (int)($skin1g*$w1 + $skin2g*$w2 + $skin3g*$w3);
            $b = (int)($skin1b*$w1 + $skin2b*$w2 + $skin3b*$w3);
            return ($r << 16) + ($g << 8) + ($b);
        };
    }

//REGION Colored
/////////////////////////////////////////////////
//REGION Textured

    static function initTexturedPoint(&$skin1)
    {
        if(!self::$_texture) return function() { return 0x00000000; };
        $_tRes = &self::$_texture;
        $textureW = imagesx($_tRes);
        $textureH = imagesy($_tRes);
        $t1x = $textureW * $skin1[0];
        $t1y = $textureH * $skin1[1];
        return function() use(&$_tRes, &$t1x, &$t1y)
        {
            return imagecolorat($_tRes, $t1x, $t1y);
        };
    }

    static function initTexturedLine(&$skin1, &$skin2)
    {
        if(!self::$_texture) return function() { return 0x00000000; };
        $_tRes = &self::$_texture;
        $textureW = imagesx($_tRes)-1;
        $textureH = imagesy($_tRes)-1;
        $t1x = $textureW * $skin1[0];
        $t1y = $textureH * $skin1[1];
        $t2x = $textureW * $skin2[0];
        $t2y = $textureH * $skin2[1];
        return function(&$w1, &$w2) use(&$_tRes, &$t1x, &$t1y, &$t2x, &$t2y)
        {
            return imagecolorat($_tRes, $w1*$t1x + $w2*$t2x, $w1*$t1y + $w2*$t2y);
        };
    }

    static function initTexturedTriangle(&$skin1, &$skin2, &$skin3)
    {
        if(!self::$_texture)
            return function()
            {
                return (rand(0, 255) << 16) + (rand(0, 255) << 8) + (rand(0, 255));
            };
        $_tRes = &self::$_texture;
        $textureW = imagesx($_tRes)-1;
        $textureH = imagesy($_tRes)-1;
        $t1x = $textureW * $skin1[0];
        $t1y = $textureH * $skin1[1];
        $t2x = $textureW * $skin2[0];
        $t2y = $textureH * $skin2[1];
        $t3x = $textureW * $skin3[0];
        $t3y = $textureH * $skin3[1];
        return function($w1, $w2, $w3) use($_tRes, $t1x, $t1y, $t2x, $t2y, $t3x, $t3y)
        {
            return imagecolorat($_tRes, $w1*$t1x + $w2*$t2x + $w3*$t3x, $w1*$t1y + $w2*$t2y + $w3*$t3y);
        };
    }

//REGION Textured
/////////////////////////////////////////////////

}
