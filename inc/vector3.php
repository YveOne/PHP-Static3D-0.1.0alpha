<?php

// (Numeric) $x
// (Numeric) $y
// (Numeric) $z
// (Vector3) [$x, $y, $z]

// $v = Vector3::multiplyByMatrix($v, $m);
// $d = Vector3::distance($from, $to);
// $v = Vector3::normalize($v);
// $d = Vector3::dot($l, $r);
// $v = Vector3::cross($l, $r);
// $v = Vector3::barycentric($t1, $t2, $t3, $b2, $b3);
// $v = Vector3::catmullRom($p1, $p2, $p3, $p4, $t);
// $v = Vector3::clamp($v, $min, $max);
// $v = Vector3::sum($l, $r);
// $v = Vector3::subtract($l, $r);
// $equal = Vector3::equal($l, $r, $precision);
// $v = Vector3::hermite($fromP, $fromT, $toP, $toT, $w);
// $l = Vector3::length($v);
// $l = Vector3::lengthSquared($v);
// $v = Vector3::lerp($from, $to, $w);
// $v = Vector3::min($l, $r);
// $v = Vector3::max($l, $r);
// $v = Vector3::scale($l, $r);
// $v = Vector3::negate($v);
// $v = Vector3::reflect($source, $normal);
// $v = Vector3::smoothStep($start, $end, $amount);

class Vector3
{
    private function __construct(){}

    /**
    * Perform a post-multiplication: row vector * matrix on right
    *
    * $v = Vector3::multiplyByMatrix($v, $m);
    */
    static function multiplyByMatrix($v, $m)
    {
        $v0 = $v[0];
        $v1 = $v[1];
        $v2 = $v[2];
        //return [
        //    $v0*$m[0]  + $v1*$m[1]  + $v2*$m[2]  + $m[3],
        //    $v0*$m[4]  + $v1*$m[5]  + $v2*$m[6]  + $m[7],
        //    $v0*$m[8]  + $v1*$m[9]  + $v2*$m[10] + $m[11]
        //];
        return [
            ( $v0*$m[0]  + $v1*$m[1]  + $v2*$m[2]  + $v[3]*$m[3]  ),
            ( $v0*$m[4]  + $v1*$m[5]  + $v2*$m[6]  + $v[3]*$m[7]  ),
            ( $v0*$m[8]  + $v1*$m[9]  + $v2*$m[10] + $v[3]*$m[11] ),
            ( $v0*$m[12] + $v1*$m[13] + $v2*$m[14] + $v[3]*$m[15] )
        ];
    }

    /**
    * Calculates the distance between two vectors.
    *
    * $d = Vector3::distance($from, $to);
    */
    static function distance($from, $to)
    {
        $x = $to[0] - $from[0];
        $y = $to[1] - $from[1];
        $z = $to[2] - $from[2];
        return sqrt($x*$x + $y*$y + $z*$z);
    }

    /**
    * Creates a unit vector from the specified vector. The result
    * is a  vector  one  unit  in  length  pointing  in  the same
    * direction as the original vector.
    *
    * $v = Vector3::normalize($v);
    */
    static function normalize($v)
    {
        $x = $v[0];
        $y = $v[1];
        $z = $v[2];
        $d = sqrt($x*$x+$y*$y+$z*$z);
        if($d==0) return $v;
        return [
            $x / $d,
            $y / $d,
            $z / $d
        ];
    }

    /**
    * Calculates the  dot  product  of  two  vectors.  If the two
    * vectors  are  unit  vectors,  the  dot  product  returns  a
    * floating point value  between  -1 and 1 that can be used to
    * determine some properties of the angle between two vectors.
    * For  example,  it   can   show   whether  the  vectors  are
    * orthogonal, parallel,  or  have  an  acute  or obtuse angle
    * between them.
    *
    * $d = Vector3::dot($l, $r);
    */
    static function dot($l, $r)
    {
        return $l[0]*$r[0] + $l[1]*$r[1] + $l[2]*$r[2];
    }

    /**
    * Calculates the cross product of two vectors.
    *
    * $v = Vector3::cross($l, $r);
    */
    static function cross($l, $r)
    {
        $x1 = $l[0];
        $y1 = $l[1];
        $z1 = $l[2];
        $x2 = $r[0];
        $y2 = $r[1];
        $z2 = $r[2];
        return [
            $y1*$z2 - $y2*$z1,
            $z1*$x2 - $z2*$x1,
            $x1*$y2 - $x2*$y1
        ];
    }

    /**
    * Returns a Vector3 containing  the  3D Cartesian coordinates
    * of a point specified in Barycentric coordinates relative to
    * a 3D triangle.
    *
    * (Vector3) $v = Vector3::barycentric(
    *     (Vector3) $t1, // A Vector3 containing the 3D Cartesian coordinates of vertex 1 of the triangle
    *     (Vector3) $t2, // A Vector3 containing the 3D Cartesian coordinates of vertex 2 of the triangle
    *     (Vector3) $t3, // A Vector3 containing the 3D Cartesian coordinates of vertex 3 of the triangle
    *     (Numeric) $b2, // Barycentric coordinate, which expresses the weighting factor toward vertex 2 (specified in $t2)
    *     (Numeric) $b3  // Barycentric coordinate, which expresses the weighting factor toward vertex 3 (specified in $t3)
    * );
    */
    static function barycentric($t1, $t2, $t3, $b2, $b3)
    {
        $b1 = 1 - $b2 - $b3;
        return [
            $b1*$t1[0] + $b2*$t2[0] + $b3*$t3[0],
            $b1*$t1[1] + $b2*$t2[1] + $b3*$t3[1],
            $b1*$t1[2] + $b2*$t2[2] + $b3*$t3[2]
        ];
    }

    /**
    * Performs a Catmull-Rom interpolation using the specified positions.
    *
    * (Vector3) $result = Vector3::catmullRom(
    *     (Vector3) $p1, // The first position in the interpolation
    *     (Vector3) $p2, // The second position in the interpolation
    *     (Vector3) $p3, // The third position in the interpolation
    *     (Vector3) $p4, // The fourth position in the interpolation
    *     (Numeric) $t   // Weighting factor
    * );
    */
    static function catmullRom($p1, $p2, $p3, $p4, $t)
    {
        $t2 = $t*$t;
        $t3 = $t2*$t;
        $f1 = -0.5*$t3 +     $t2 - 0.5*$t;
        $f2 =  1.5*$t3 - 2.5*$t2 + 1.0;
        $f3 = -1.5*$t3 + 2.0*$t2 + 0.5*$t;
        $f4 =  0.5*$t3 - 0.5*$t2;
        return [
            $p1[0]*$f1 + $p2[0]*$f2 + $p3[0]*$f3 + $p4[0]*$f4,
            $p1[1]*$f1 + $p2[1]*$f2 + $p3[1]*$f3 + $p4[1]*$f4,
            $p1[2]*$f1 + $p2[2]*$f2 + $p3[2]*$f3 + $p4[2]*$f4
        ];
    }

    /**
    * Restricts a value to be within a specified range.
    *
    * $min = (Vector3) | (Numeric)
    * $max = (Vector3) | (Numeric)
    * $v = Vector3::clamp($v, $min, $max);
    */
    static function clamp($v, $min, $max)
    {
        $x = $v[0];
        $y = $v[1];
        $z = $v[2];
        if(is_array($min))
        {
            $xMin = $min[0];
            $yMin = $min[1];
            $zMin = $min[2];
        }
        else
        {
            $xMin = $yMin = $zMin = $min;
        }
        if(is_array($max))
        {
            $xMax = $max[0];
            $yMax = $max[1];
            $zMax = $max[2];
        }
        else
        {
            $xMax = $yMax = $zMax = $max;
        }
        return [
            ($x < $xMin) ? $xMin : (($x > $xMax) ? $xMax : ($x)),
            ($y < $yMin) ? $yMin : (($y > $yMax) ? $yMax : ($y)),
            ($z < $zMin) ? $zMin : (($z > $zMax) ? $zMax : ($z))
        ];
    }

    /**
    * Addition
    *
    * $add = (Vector3) | (Numeric)
    * $v = Vector3::sum($v, $add);
    */
    static function sum($l, $r)
    {
        if(is_array($l))
        {
            $x1 = $l[0];
            $y1 = $l[1];
            $z1 = $l[2];
        }
        else
        {
            $x1 = $y1 = $z1 = $l;
        }
        if(is_array($r))
        {
            $x2 = $r[0];
            $y2 = $r[1];
            $z2 = $r[2];
        }
        else
        {
            $x2 = $y2 = $z2 = $r;
        }
        return [
            $x1+$x2,
            $y1+$y2,
            $z1+$z2
        ];
    }

    /**
    * Subtraction
    *
    * $l = (Vector3) | (Numeric)
    * $r = (Vector3) | (Numeric)
    * $v = Vector3::subtract($l, $r);
    */
    static function subtract($l, $r)
    {
        if(is_array($l))
        {
            $x1 = $l[0];
            $y1 = $l[1];
            $z1 = $l[2];
        }
        else
        {
            $x1 = $y1 = $z1 = $l;
        }
        if(is_array($r))
        {
            $x2 = $r[0];
            $y2 = $r[1];
            $z2 = $r[2];
        }
        else
        {
            $x2 = $y2 = $z2 = $r;
        }
        return [
            $x1-$x2,
            $y1-$y2,
            $z1-$z2
        ];
    }

    /**
    * Returns a value that indicates whether the current instance
    * is equal to a specified object.
    *
    * $equal = Vector3::equal($l, $r, $precision);
    */
    static function equal($l, $r, $precision=3)
    {
        if (round($l[0]-$r[0], $precision) != 0) return false;
        if (round($l[1]-$r[1], $precision) != 0) return false;
        if (round($l[2]-$r[2], $precision) != 0) return false;
        return true;
    }

    /**
    * Performs a Hermite spline interpolation.
    *
    * (Vector3) $v = Vector3::hermite(
    *   (Vector3) $fromP,  // Source position vector
    *   (Vector3) $fromT,  // Source tangent vector
    *   (Vector3) $toP,    // Destination position vector
    *   (Vector3) $toT,    // Destination tangent vector
    *   (Numeric) $w       // Weighting factor
    * );
    */
    static function hermite($p1, $t1, $p2, $t2, $t)
    {
        $t2 = $t*$t;
        $t3 = $t2*$t;
        $h1 =  2*$t3 - 3*$t2 + 1;
        $h2 = -2*$t3 + 3*$t2;
        $h3 =    $t3 - 2*$t2 + $t;
        $h4 =    $t3 -   $t2;
        return [
            $p1[0]*$h1 + $p2[0]*$h2 + $t1[0]*$h3 + $t2[0]*$h4,
            $p1[1]*$h1 + $p2[1]*$h2 + $t1[1]*$h3 + $t2[1]*$h4,
            $p1[2]*$h1 + $p2[2]*$h2 + $t1[2]*$h3 + $t2[2]*$h4
        ];
    }

    /**
    * Calculates the length of the vector.
    *
    * $l = Vector3::length($v);
    */
    static function length($v)
    {
        $x = $v[0];
        $y = $v[1];
        $z = $v[2];
        return sqrt( $x*$x + $y*$y + $z*$z );
    }

    /**
    * Calculates the length of the vector (squared).
    *
    * $l = Vector3::lengthSquared($v);
    */
    static function lengthSquared($v)
    {
        $x = $v[0];
        $y = $v[1];
        $z = $v[2];
        return ( $x*$x + $y*$y + $z*$z );
    }

    /**
    * Performs a linear interpolation between two vectors.
    *
    * $v = Vector3::lerp($from, $to, $w);
    */
    static function lerp($from, $to, $w)
    {
        $fromX = $from[0];
        $fromY = $from[1];
        $fromZ = $from[2];
        return [
            $fromX + ($to[0]-$fromX) * $w,
            $fromY + ($to[1]-$fromY) * $w,
            $fromZ + ($to[2]-$fromZ) * $w
        ];
    }

    /**
    * Returns a vector that  contains  the lowest
    * value from each matching pair of components.
    *
    * $l = (Vector3) | (Numeric)
    * $r = (Vector3) | (Numeric)
    * $v = Vector3::min($l, $r);
    */
    static function min($l, $r)
    {
        if(is_array($l))
        {
            $x1 = $l[0];
            $y1 = $l[1];
            $z1 = $l[2];
        }
        else
        {
            $x1 = $y1 = $z1 = $l;
        }
        if(is_array($r))
        {
            $x2 = $r[0];
            $y2 = $r[1];
            $z2 = $r[2];
        }
        else
        {
            $x2 = $y2 = $z2 = $r;
        }
        return [
            ($x1<=$x2)?$x1:$x2,
            ($y1<=$y2)?$y1:$y2,
            ($z1<=$z2)?$z1:$z2
        ];
    }

    /**
    * Returns a vector that  contains  the highest
    * value from each matching pair of components.
    *
    * $l = (Vector3) | (Numeric)
    * $r = (Vector3) | (Numeric)
    * $v = Vector3::max($l, $r);
    */
    static function max($l, $r)
    {
        if(is_array($l))
        {
            $x1 = $l[0];
            $y1 = $l[1];
            $z1 = $l[2];
        }
        else
        {
            $x1 = $y1 = $z1 = $l;
        }
        if(is_array($r))
        {
            $x2 = $r[0];
            $y2 = $r[1];
            $z2 = $r[2];
        }
        else
        {
            $x2 = $y2 = $z2 = $r;
        }
        return [
            ($x1>=$x2)?$x1:$x2,
            ($y1>=$y2)?$y1:$y2,
            ($z1>=$z2)?$z1:$z2
        ];
    }

    /**
    * Multiplies a vector by a scalar value.
    *
    * $l = (Vector3) | (Numeric)
    * $r = (Vector3) | (Numeric)
    * $v = Vector3::scale($l, $r);
    */
    static function scale($l, $r)
    {
        if(is_array($l))
        {
            $x1 = $l[0];
            $y1 = $l[1];
            $z1 = $l[2];
        }
        else
        {
            $x1 = $y1 = $z1 = $l;
        }
        if(is_array($r))
        {
            $x2 = $r[0];
            $y2 = $r[1];
            $z2 = $r[2];
        }
        else
        {
            $x2 = $y2 = $z2 = $r;
        }
        return [
            $x1*$x2,
            $y1*$y2,
            $z1*$z2
        ];
    }

    /**
    * Returns a vector pointing in the opposite direction.
    *
    * $v = Vector3::negate($v);
    */
    static function negate($v)
    {
        return [
            -$v[0],
            -$v[1],
            -$v[2]
        ];
    }

    /**
    * Returns the reflection of  a  vector off a surface that has
    * the specified normal.
    *
    * $v = Vector3::reflect($source, $normal);
    */
    static function reflect($source, $normal)
    {
        $dotN2 = self::dot($source, $normal) * -2;
        return [
            $dotN2 * $normal[0] + $source[0],
            $dotN2 * $normal[1] + $source[1],
            $dotN2 * $normal[2] + $source[2]
        ];
    }

    /**
    * Interpolates between two values using a cubic equation.
    *
    * $v = Vector3::smoothStep($start, $end, $amount);
    */
    static function smoothStep($from, $to, $amount)
    {
        $fromX = $from[0];
        $fromY = $from[1];
        $fromZ = $from[2];
        // Clamp to 0-1
        $amount = ($amount >= 1) ? 1 : (($amount <= 0) ? 0 : $amount);
        // Cubicly adjust the amount value
        $amount = ($amount * $amount) * (3 - (2 * $amount));
        return [
            ($to[0] - $fromX) * $amount + $fromX,
            ($to[1] - $fromY) * $amount + $fromY,
            ($to[2] - $fromZ) * $amount + $fromZ
        ];
    }

}
