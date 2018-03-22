<?php

// clip near/far (3d)
// lineZ(&$p1, &$p2, $minZ, $maxZ)
// polyZ(&$poly, $minZ, $maxZ)

// clip screen (2d)
// line2(&$p1, &$p2)
// poly2(&$poly)

define("S3D_CLIP_TOP", 1);
define("S3D_CLIP_LEFT", 2);
define("S3D_CLIP_RIGHT", 4);
define("S3D_CLIP_BOTTOM", 8);
define("S3D_CLIP_NEAR", 16);
define("S3D_CLIP_FAR", 32);

class S3D_Clipping
{
    private function __construct(){}

    static function lineZ(&$p1, &$p2, $minZ, $maxZ)
    {
        if($p1[2] < $minZ && $p2[2] < $minZ) return false;
        if($p1[2] > $maxZ && $p2[2] > $maxZ) return false;

        $x1 = &$p1[0];
        $y1 = &$p1[1];
        $z1 = &$p1[2];
        $x2 = &$p2[0];
        $y2 = &$p2[1];
        $z2 = &$p2[2];
        $dx = $x2 - $x1;
        $dy = $y2 - $y1;
        $dz = $z2 - $z1;

        $K1 = 0;
        if($z1 < $minZ) $K1 = S3D_CLIP_NEAR;
        elseif($z1 > $maxZ) $K1 = S3D_CLIP_FAR;

        $K2 = 0;
        if($z2 < $minZ) $K2 = S3D_CLIP_NEAR;
        elseif($z2 > $maxZ) $K2 = S3D_CLIP_FAR;

        while($K1 || $K2)
        {
            if($K1 & $K2) return false;
            if($K1)
            {
                if($K1 & S3D_CLIP_NEAR)
                {
                    $mul = ($minZ - $z1) / $dz;
                    $x1 += $dx * $mul;
                    $y1 += $dy * $mul;
                    $z1  = $minZ;
                }
                elseif($K1 & S3D_CLIP_FAR)
                {
                    $mul = ($maxZ - $z1) / $dz;
                    $x1 += $dx * $mul;
                    $y1 += $dy * $mul;
                    $z1  = $minZ;
                }
                $K1 = 0;
                if($z1 < $minZ) $K1 = S3D_CLIP_NEAR;
                elseif($z1 > $maxZ) $K1 = S3D_CLIP_FAR;
            }
            if($K1 & $K2) return false;
            if($K2)
            {
                if($K2 & S3D_CLIP_NEAR)
                {
                    $mul = ($minZ - $z2) / $dz;
                    $x2 += $dx * $mul;
                    $y2 += $dy * $mul;
                    $z2  = $minZ;
                }
                elseif($K2 & S3D_CLIP_FAR)
                {
                    $mul = ($maxZ - $z2) / $dz;
                    $x2 += $dx * $mul;
                    $y2 += $dy * $mul;
                    $z2  = $minZ;
                }
                $K2 = 0;
                if($z2 < $minZ) $K2 = S3D_CLIP_NEAR;
                elseif($z2 > $maxZ) $K2 = S3D_CLIP_FAR;
            }
        }
        return true;
    }

    static function line2(&$p1, &$p2)
    {
        $x1 = &$p1[0];
        $y1 = &$p1[1];
        $x2 = &$p2[0];
        $y2 = &$p2[1];
        $dx = $x2 - $x1;
        $dy = $y2 - $y1;

        $K1 = 0;
        if($y1 < -1) $K1 = S3D_CLIP_BOTTOM;
        elseif($y1 > 1) $K1 = S3D_CLIP_TOP;
        if($x1 < -1) $K1 |= S3D_CLIP_LEFT;
        elseif($x1 > 1) $K1 |= S3D_CLIP_RIGHT;

        $K2 = 0;
        if($y2 < -1) $K2 = S3D_CLIP_BOTTOM;
        elseif($y2 > 1) $K2 = S3D_CLIP_TOP;
        if($x2 < -1) $K2 |= S3D_CLIP_LEFT;
        elseif($x2 > 1) $K2 |= S3D_CLIP_RIGHT;

        while($K1 || $K2)
        {
            if($K1 & $K2) return false;
            if($K1)
            {
                if($K1 & S3D_CLIP_LEFT)
                {
                    $y1 += (-1-$x1)*$dy/$dx;
                    $x1  = -1;
                }
                elseif($K1 & S3D_CLIP_RIGHT)
                {
                    $y1 += (1-$x1)*$dy/$dx;
                    $x1  = 1;
                }
                if($K1 & S3D_CLIP_BOTTOM)
                {
                    $x1 += (-1-$y1)*$dx/$dy;
                    $y1  = -1;
                }
                elseif($K1 & S3D_CLIP_TOP)
                {
                    $x1 += (1-$y1)*$dx/$dy;
                    $y1  = 1;
                }
                $K1 = 0;
                if($y1 < -1) $K1 = S3D_CLIP_BOTTOM;
                elseif($y1 > 1) $K1 = S3D_CLIP_TOP;
                if($x1 < -1) $K1 |= S3D_CLIP_LEFT;
                elseif($x1 > 1) $K1 |= S3D_CLIP_RIGHT;
            }

            if($K1 & $K2) return false;
            if($K2)
            {
                if($K2 & S3D_CLIP_LEFT)
                {
                    $y2 += (-1-$x2)*$dy/$dx;
                    $x2  = -1;
                }
                elseif($K2 & S3D_CLIP_RIGHT)
                {
                    $y2 += (1-$x2)*$dy/$dx;
                    $x2  = 1;
                }
                if($K2 & S3D_CLIP_BOTTOM)
                {
                    $x2 += (-1-$y2)*$dx/$dy;
                    $y2  = -1;
                }
                elseif($K2 & S3D_CLIP_TOP)
                {
                    $x2 += (1-$y2)*$dx/$dy;
                    $y2  = 1;
                }
                $K2 = 0;
                if($y2 < -1) $K2 = S3D_CLIP_BOTTOM;
                elseif($y2 > 1) $K2 = S3D_CLIP_TOP;
                if($x2 < -1) $K2 |= S3D_CLIP_LEFT;
                elseif($x2 > 1) $K2 |= S3D_CLIP_RIGHT;
            }
        }
        return true;
    }

    static function polyZ(&$poly, $minZ, $maxZ)
    {
        // min z
        $in = $poly;
        $out = [];
        $s = end($in);
        foreach ($in as $e) {
            if ($s[2] >= $minZ) {
                if ($e[2] >= $minZ) {
                    $out[] = $e;
                }
                else
                {
                    $out[] = self::zIntersection($s, $e, $minZ);
                }
            }
            else if ($e[2] >= $minZ) {
                $out[] = self::zIntersection($s, $e, $minZ);
                $out[] = $e;
            }
            $s = $e;
        }
        // max z
        $in = $out;
        $out = [];
        $s = end($in);
        foreach ($in as $e) {
            if ($s[2] <= $maxZ) {
                if ($e[2] <= $maxZ) {
                    $out[] = $e;
                }
                else
                {
                    $out[] = self::zIntersection($s, $e, $maxZ);
                }
            }
            else if ($e[2] <= $maxZ) {
                $out[] = self::zIntersection($s, $e, $maxZ);
                $out[] = $e;
            }
            $s = $e;
        }
        $poly = $out;
        return true;
    }

    static function poly2(&$poly)
    {
        $clip = [[-1, -1], [1, -1], [1, 1], [-1, 1]];
        $out = $poly;
        $cp1 = end($clip);
        foreach ($clip as $cp2)
        {
            $in = $out;
            $out = [];
            $s = end($in);
            foreach ($in as $e)
            {
                if(self::insideLine2($e, $cp1, $cp2))
                {
                    if(!self::insideLine2($s, $cp1, $cp2))
                    {
                        $out[] = self::intersection2($cp1, $cp2, $e, $s);
                    }
                    $out[] = $e;
                }
                elseif (self::insideLine2($s, $cp1, $cp2))
                {
                    $out[] = self::intersection2($cp1, $cp2, $e, $s);
                }
                $s = $e;
            }
            $cp1 = $cp2;
        }
        $poly = $out;
        return true;
    }

// privates

    static private function zIntersection(&$p1, &$p2, $z)
    {
        $dirX = $p2[0] - $p1[0];
        $dirY = $p2[1] - $p1[1];
        $dirZ = $p2[2] - $p1[2];
        $mul = ($z - $p1[2]) / $dirZ;
        return [
            $p1[0] + ($dirX * $mul),
            $p1[1] + ($dirY * $mul),
            $z,
            1
        ];
    }

    static private function intersection2(&$cp1, &$cp2, &$e, &$s)
    {
        $dcX = $cp1[0] - $cp2[0];
        $dcY = $cp1[1] - $cp2[1];
        $dpX = $s[0] - $e[0];
        $dpY = $s[1] - $e[1];
        $n1 = $cp1[0] * $cp2[1] - $cp1[1] * $cp2[0];
        $n2 = $s[0] * $e[1] - $s[1] * $e[0];
        $n3 = 1.0 / ($dcX * $dpY - $dcY * $dpX);
        return [
            ($n1*$dpX - $n2*$dcX) * $n3,
            ($n1*$dpY - $n2*$dcY) * $n3
        ];
    }

    static private function insideLine2(&$p, &$cp1, &$cp2) {
        return ($cp2[0]-$cp1[0])*($p[1]-$cp1[1]) > ($cp2[1]-$cp1[1])*($p[0]-$cp1[0]);
    }


}
