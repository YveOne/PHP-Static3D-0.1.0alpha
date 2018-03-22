<?php

// (Numeric) $x
// (Numeric) $y
// (Vector2) [$x, $y]

// distance(&$from, &$to)
// distanceSquared(&$from, &$to)
// normalize(&$v)
// length(&$v)
// lengthSquared(&$v)
// addVec(&$vec, &$add)
// addNum(&$vec, $add)
// subtractL(&$val, $sub)
// subtractR($val, &$sub)
// subtractLR(&$val, &$sub)
// clampVec(&$v, &$min, &$max)
// clampNum(&$v, $min, $max)
// dot(&$l, &$r)
// cross(&$l, &$r)
// equal(&$left, &$right, $precision=3)
// lerp(&$from, &$to, $w)
// minVec(&$l, &$r)
// minNum(&$l, $r)
// scaleVec(&$v, &$val)
// scaleNum(&$v, $val)
// negate(&$v)
// reflect(&$source, &$normal)

class Vector2
{
    private function __construct(){}

    /**
    * Calculates the distance between two vectors.
    *
    * (Numeric) $dist = Vector2::distance(
    *   (Vector2) &$from,
    *   (Vector2) &$to
    * );
    */
    static function distance(&$from, &$to)
    {
        $x = $to[0] - $from[0];
        $y = $to[1] - $from[1];
        return sqrt($x*$x + $y*$y);
    }

    /**
    * Calculates the distance between two vectors squared.
    *
    * (Numeric) $dist = Vector2::distanceSquared(
    *   (Vector2) &$from,
    *   (Vector2) &$to
    * );
    */
    static function distanceSquared(&$from, &$to)
    {
        $x = $to[0] - $from[0];
        $y = $to[1] - $from[1];
        return ($x*$x + $y*$y);
    }

    /**
    * Creates a unit vector from the specified vector. The result
    * is a  vector  one  unit  in  length  pointing  in  the same
    * direction as the original vector.
    *
    * (Vector2) $unit = Vector2::normalize(
    *   (Vector2) &$v
    * );
    */
    static function normalize(&$v)
    {
        $d = sqrt( $v[0]*$v[0] + $v[1]*$v[1] );
        if($d==0) return $v;
        return [
            $v[0] / $d,
            $v[1] / $d
        ];
    }

    /**
    * Calculates the length of the vector.
    *
    * (Numeric) $len = Vector2::length(
    *   (Vector2) &$v
    * );
    */
    static function length(&$v)
    {
        return sqrt( $v[0]*$v[0] + $v[1]*$v[1] );
    }

    /**
    * Calculates the length of the vector (squared).
    *
    * (Numeric) $len = Vector2::lengthSquared(
    *   (Vector2) &$v
    * );
    */
    static function lengthSquared(&$v)
    {
        return ( $v[0]*$v[0] + $v[1]*$v[1] );
    }

    /**
    * Addition
    *
    * (Vector2) $ret = Vector2::addVec(
    *   (Vector2) &$left,
    *   (Vector2) &$right
    * (Vector2) $ret = Vector2::addNum(
    *   (Vector2) &$left,
    *   (Numeric) $right
    * );
    */
    static function addVec(&$vec, &$add)
    {
        return [
            $v[0] + $add[0],
            $v[1] + $add[1]
        ];
    }
    static function addNum(&$vec, $add)
    {
        return [
            $v[0] + $add,
            $v[1] + $add
        ];
    }

    /**
    * Subtraction
    *
    * (Vector2) $ret = Vector2::subtractL(
    *   (Vector2) &$vec,
    *   (Numeric) $num
    * );
    * (Vector2) $ret = Vector2::subtractR(
    *   (Numeric) $num,
    *   (Vector2) &$vec
    * );
    * (Vector2) $ret = Vector2::subtractLR(
    *   (Vector2) &$vec1,
    *   (Vector2) &$vec2
    * );
    */
    static function subtractL(&$val, $sub)
    {
        return [
                $val[0] - $sub,
                $val[1] - $sub
        ];
    }
    static function subtractR($val, &$sub)
    {
        return [
            $val - $sub[0],
            $val - $sub[1]
        ];
    }
    static function subtractLR(&$val, &$sub)
    {
        return [
            $val[0] - $sub[0],
            $val[1] - $sub[1]
        ];
    }

    /**
    * Restricts a value to be within a specified range.
    *
    * (Vector2) $clamped = Vector2::clampVec(
    *   (Vector2) &$value,
    *   (Vector2) &$min,
    *   (Vector2) &$max
    * );
    * Vector2 $clamped = Vector2::clampNum(
    *   (Vector2) &$value,
    *   (Numeric) $min,
    *   (Numeric) $max
    * );
    */
    static function clampVec(&$v, &$min, &$max)
    {
        return [
            ($v[0] < $min[0]) ? $min[0] : (($v[0] > $max[0]) ? $max[0] : ($v[0])),
            ($v[1] < $min[1]) ? $min[1] : (($v[1] > $max[1]) ? $max[1] : ($v[1]))
        ];
    }
    static function clampNum(&$v, $min, $max)
    {
        return [
            ($v[0] < $min) ? $min : (($v[0] > $max) ? $max : ($v[0])),
            ($v[1] < $min) ? $min : (($v[1] > $max) ? $max : ($v[1]))
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
    * (Numeric) $dot = Vector2::dot(
    *   (Vector2) &$l,
    *   (Vector2) &$r
    * );
    */
    static function dot(&$l, &$r)
    {
        return $l[0]*$r[0] + $l[1]*$r[1];
    }

    /**
    * Calculates the cross product of two vectors.
    *
    * (Vector2) $cross = Vector2::cross(
    *   (Vector2) &$left,
    *   (Vector2) &$right
    * );
    */
    static function cross(&$l, &$r)
    {
        //return [
        //    $v[1],
        //    -$v[0]
        //];
        return $l[0]*$r[1] - $l[1]*$r[0];
    }

    /**
    * Returns a value that indicates whether the current instance
    * is equal to a specified object.
    *
    * (Boolean) $equal = Vector2::equal(
    *   (Vector2) &$left,
    *   (Vector2) &$right,
    *   (Numeric|Optional) $precision=3
    * );
    */
    static function equal(&$left, &$right, $precision=3)
    {
        if (round($left[0]-$right[0], $precision) != 0) return false;
        if (round($left[1]-$right[1], $precision) != 0) return false;
        return true;
    }

    /**
    * Performs a linear interpolation between two vectors.
    *
    * (Vector2) $return = Vector2::lerp(
    *   (Vector2) &$from,
    *   (Vector2) &$to,
    *   (Numeric) $w
    * );
    */
    static function lerp(&$from, &$to, $w)
    {
        return [
            $from[0] + ($to[0]-$from[0]) * $w,
            $from[1] + ($to[1]-$from[1]) * $w
        ];
    }

    /**
    * Returns a vector that  contains  the lowest value from each
    * matching pair of components.
    *
    * (Vector2) $ret = Vector2::minVec(
    *   (Vector2) &$left,
    *   (Vector2) &$right
    * );
    * (Vector2) $ret = Vector2::minNum(
    *   (Vector2) &$left,
    *   (Numeric) $right
    * );
    */
    static function minVec(&$l, &$r)
    {
        return [
            ($l[0] <= $r[0]) ? $l[0] : $r[0],
            ($l[1] <= $r[1]) ? $l[1] : $r[1]
        ];
    }
    static function minNum(&$l, $r)
    {
        return [
            ($l[0] <= $r) ? $l[0] : $r,
            ($l[1] <= $r) ? $l[1] : $r
        ];
    }

    /**
    * Multiplies a vector by a scalar value.
    *
    * (Vector2) $result = Vector2::scaleVec(
    *   (Vector2) &$vec,
    *   (Vector2) &$val
    * );
    * (Vector2) $result = Vector2::scaleNum(
    *   (Vector2) &$vec,
    *   (Numeric) $val
    * );
    */
    static function scaleVec(&$v, &$val)
    {
        return [
            $v[0] * $val[0],
            $v[1] * $val[1]
        ];
    }
    static function scaleNum(&$v, $val)
    {
        return [
            $v[0] * $val,
            $v[1] * $val
        ];
    }

    /**
    * Returns a vector pointing in the opposite direction.
    *
    * (Vector2) $out = Vector2::negate(
    *   (Vector2) &$in
    * );
    */
    static function negate(&$v)
    {
        return [
            -$v[0],
            -$v[1]
        ];
    }

    /**
    * Returns the reflection of  a  vector of a surface that has
    * the specified normal.
    *
    * (Vector2) $reflected = Vector2::reflect(
    *   (Vector2) &$source,
    *   (Vector2) &$normal
    * );
    */
    static function reflect(&$source, &$normal)
    {
        $dot = self::dot($source, $normal) * -2;
        return [
            $dot * $normal[0] + $source[0],
            $dot * $normal[1] + $source[1]
        ];
    }

}
