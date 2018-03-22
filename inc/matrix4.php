<?php

// (Matrix4) [...]

// $m = Matrix4::zero();
// $m = Matrix4::identity();
// $m = Matrix4::scale($sx, $sy, $sz);
// $m = Matrix4::translation($tx, $ty, $tz);
// $m = Matrix4::rotationX($rad);
// $m = Matrix4::rotationY($rad);
// $m = Matrix4::rotationZ($rad);
// $d = Matrix4::determinant($m);
// $m = Matrix4::transpose($m);
// $m = Matrix4::invert($m);
// $m = Matrix4::appendMatrix($m1, $m2);
// $m = Matrix4::prependMatrix($m1, $m2);
// $m = Matrix4::frustum($left, $right, $bottom, $top, $near, $far);
// $m = Matrix4::perspectiveFovLH($fovY, $aspect, $near, $far);
// $m = Matrix4::lookAtLH($camera, $target, $up);
// $m = Matrix4::lookAtRH($camera, $target, $up);
// $m = Matrix4::rotationYawPitchRoll($z, $y, $x);

class Matrix4
{
    private function __construct(){}

    static private $m_zero = [
        0,0,0,0,
        0,0,0,0,
        0,0,0,0,
        0,0,0,0
    ];
    static private $m_identity = [
        1,0,0,0,
        0,1,0,0,
        0,0,1,0,
        0,0,0,1
    ];
    static private $m_scale = [
        1,0,0,0,
        0,1,0,0,
        0,0,1,0,
        0,0,0,1
    ];
    static private $m_translate = [
        1,0,0,0,
        0,1,0,0,
        0,0,1,0,
        0,0,0,1
    ];
    static private $m_rotX = [
        1,0,0,0,
        0,1,0,0,
        0,0,1,0,
        0,0,0,1
    ];
    static private $m_rotY = [
        1,0,0,0,
        0,1,0,0,
        0,0,1,0,
        0,0,0,1
    ];
    static private $m_rotZ = [
        1,0,0,0,
        0,1,0,0,
        0,0,1,0,
        0,0,0,1
    ];

    /**
    * Retrieves an empty matrix.
    *
    * $m = Matrix4::zero();
    */
    static function zero()
    {
        return self::$m_zero;
    }

    /**
    * Retrieves the identity of the matrix.
    *
    * $m = Matrix4::identity();
    */
    static function identity()
    {
        return self::$m_identity;
    }

    /**
    * Returns a scaling matrix.
    *
    * $m = Matrix4::scale($sx, $sy, $sz);
    */
    static function scale($sx, $sy, $sz)
    {
        self::$m_scale[0]  = $sx;
        self::$m_scale[5]  = $sy;
        self::$m_scale[10] = $sz;
        return self::$m_scale;
    }

    /**
    * Creates a translation matrix.
    *
    * $m = Matrix4::translation($tx, $ty, $tz);
    */
    static function translation($tx, $ty, $tz)
    {
        self::$m_translate[3]  = $tx;
        self::$m_translate[7]  = $ty;
        self::$m_translate[11] = $tz;
        return self::$m_translate;
    }

    /**
    * Creates a rotation matrix around x axis.
    *
    * $m = Matrix4::rotationX($rad);
    */
    static function rotationX($rad)
    {
        $c = cos($rad);
        $s = sin($rad);
        self::$m_rotX[5]  =  $c;
        self::$m_rotX[6]  = -$s;
        self::$m_rotX[9]  =  $s;
        self::$m_rotX[10] =  $c;
        return self::$m_rotX;
    }

    /**
    * Creates a rotation matrix around y axis.
    *
    * $m = Matrix4::rotationY($rad);
    */
    static function rotationY($rad)
    {
        $c = cos($rad);
        $s = sin($rad);
        self::$m_rotY[0]  =  $c;
        self::$m_rotY[2]  = -$s;
        self::$m_rotY[8]  =  $s;
        self::$m_rotY[10] =  $c;
        return self::$m_rotY;
    }

    /**
    * Creates a rotation matrix around z axis.
    *
    * $m = Matrix4::rotationZ($rad);
    */
    static function rotationZ($rad)
    {
        $c = cos($rad);
        $s = sin($rad);
        self::$m_rotZ[0]  =  $c;
        self::$m_rotZ[1]  = -$s;
        self::$m_rotZ[4]  =  $s;
        self::$m_rotZ[5]  =  $c;
        return self::$m_rotZ;
    }

    /**
    * Retrieves the determinant of the matrix.
    *
    * $d = Matrix4::determinant($m);
    */
    static function determinant($m)
    {
        $m00=$m[0];  $m01=$m[1];  $m02=$m[2];  $m03=$m[3];
        $m10=$m[4];  $m11=$m[5];  $m12=$m[6];  $m13=$m[7];
        $m20=$m[8];  $m21=$m[9];  $m22=$m[10]; $m23=$m[11];
        $m30=$m[12]; $m31=$m[13]; $m32=$m[14]; $m33=$m[15];
        return (
            +$m00*( $m11*($m22*$m33-$m32*$m23) - $m12*($m21*$m33-$m31*$m23) + $m13*($m21*$m32-$m31*$m22))
            -$m01*( $m10*($m22*$m33-$m32*$m23) - $m12*($m20*$m33-$m30*$m23) + $m13*($m20*$m32-$m30*$m22))
            +$m02*( $m10*($m21*$m33-$m31*$m23) - $m11*($m20*$m33-$m30*$m23) + $m13*($m20*$m31-$m30*$m21))
            -$m03*( $m10*($m21*$m32-$m31*$m22) - $m11*($m20*$m32-$m30*$m22) + $m12*($m20*$m31-$m30*$m21))
        );
    }

    /**
    * Transpose a matrix
    *
    * $m = Matrix4::transpose($m);
    */
    static function transpose($m)
    {
        $m00=$m[0];  $m01=$m[1];  $m02=$m[2];  $m03=$m[3];
        $m10=$m[4];  $m11=$m[5];  $m12=$m[6];  $m13=$m[7];
        $m20=$m[8];  $m21=$m[9];  $m22=$m[10]; $m23=$m[11];
        $m30=$m[12]; $m31=$m[13]; $m32=$m[14]; $m33=$m[15];
        return [
            $m00,$m10,$m20,$m30,
            $m01,$m11,$m21,$m31,
            $m02,$m12,$m22,$m32,
            $m03,$m13,$m23,$m33
        ];
    }

    /**
    * Calculates the inverse of a matrix.
    *
    * $_m = Matrix4::invert($m);
    */
    static function invert($m)
    {
        $m00=$m[0];  $m01=$m[1];  $m02=$m[2];  $m03=$m[3];
        $m10=$m[4];  $m11=$m[5];  $m12=$m[6];  $m13=$m[7];
        $m20=$m[8];  $m21=$m[9];  $m22=$m[10]; $m23=$m[11];
        $m30=$m[12]; $m31=$m[13]; $m32=$m[14]; $m33=$m[15];
        $n00 = $m00*$m11 - $m01*$m10;
        $n01 = $m00*$m12 - $m02*$m10;
        $n02 = $m00*$m13 - $m03*$m10;
        $n03 = $m01*$m12 - $m02*$m11;
        $n04 = $m01*$m13 - $m03*$m11;
        $n05 = $m02*$m13 - $m03*$m12;
        $n06 = $m20*$m31 - $m21*$m30;
        $n07 = $m20*$m32 - $m22*$m30;
        $n08 = $m20*$m33 - $m23*$m30;
        $n09 = $m21*$m32 - $m22*$m31;
        $n10 = $m21*$m33 - $m23*$m31;
        $n11 = $m22*$m33 - $m23*$m32;
        $d = $n00*$n11 - $n01*$n10 + $n02*$n09 + $n03*$n08 - $n04*$n07 + $n05*$n06;
        $id = 1/$d;
        $n = [];
        $n[0]  = ( $m11*$n11 - $m12*$n10 + $m13*$n09)*$id;
        $n[1]  = (-$m01*$n11 + $m02*$n10 - $m03*$n09)*$id;
        $n[2]  = ( $m31*$n05 - $m32*$n04 + $m33*$n03)*$id;
        $n[3]  = (-$m21*$n05 + $m22*$n04 - $m23*$n03)*$id;
        $n[4]  = (-$m10*$n11 + $m12*$n08 - $m13*$n07)*$id;
        $n[5]  = ( $m00*$n11 - $m02*$n08 + $m03*$n07)*$id;
        $n[6]  = (-$m30*$n05 + $m32*$n02 - $m33*$n01)*$id;
        $n[7]  = ( $m20*$n05 - $m22*$n02 + $m23*$n01)*$id;
        $n[8]  = ( $m10*$n10 - $m11*$n08 + $m13*$n06)*$id;
        $n[9]  = (-$m00*$n10 + $m01*$n08 - $m03*$n06)*$id;
        $n[10] = ( $m30*$n04 - $m31*$n02 + $m33*$n00)*$id;
        $n[11] = (-$m20*$n04 + $m21*$n02 - $m23*$n00)*$id;
        $n[12] = (-$m10*$n09 + $m11*$n07 - $m12*$n06)*$id;
        $n[13] = ( $m00*$n09 - $m01*$n07 + $m02*$n06)*$id;
        $n[14] = (-$m30*$n03 + $m31*$n01 - $m32*$n00)*$id;
        $n[15] = ( $m20*$n03 - $m21*$n01 + $m22*$n00)*$id;
        return $n;
    }

    /**
    * Appends m2 to m1
    *
    * $m = Matrix4::appendMatrix($m1, $m2);
    */
    static function appendMatrix($m1, $m2)
    {
        $a00 = $m1[0];  $a01 = $m1[1];  $a02 = $m1[2];  $a03 = $m1[3];
        $a10 = $m1[4];  $a11 = $m1[5];  $a12 = $m1[6];  $a13 = $m1[7];
        $a20 = $m1[8];  $a21 = $m1[9];  $a22 = $m1[10]; $a23 = $m1[11];
        $a30 = $m1[12]; $a31 = $m1[13]; $a32 = $m1[14]; $a33 = $m1[15];
        $b00 = $m2[0];  $b01 = $m2[1];  $b02 = $m2[2];  $b03 = $m2[3];
        $b10 = $m2[4];  $b11 = $m2[5];  $b12 = $m2[6];  $b13 = $m2[7];
        $b20 = $m2[8];  $b21 = $m2[9];  $b22 = $m2[10]; $b23 = $m2[11];
        $b30 = $m2[12]; $b31 = $m2[13]; $b32 = $m2[14]; $b33 = $m2[15];
        $m3 = [];
        $m3[0]  = $a00 * $b00 + $a01 * $b10 + $a02 * $b20 + $a03 * $b30;
        $m3[1]  = $a00 * $b01 + $a01 * $b11 + $a02 * $b21 + $a03 * $b31;
        $m3[2]  = $a00 * $b02 + $a01 * $b12 + $a02 * $b22 + $a03 * $b32;
        $m3[3]  = $a00 * $b03 + $a01 * $b13 + $a02 * $b23 + $a03 * $b33;
        $m3[4]  = $a10 * $b00 + $a11 * $b10 + $a12 * $b20 + $a13 * $b30;
        $m3[5]  = $a10 * $b01 + $a11 * $b11 + $a12 * $b21 + $a13 * $b31;
        $m3[6]  = $a10 * $b02 + $a11 * $b12 + $a12 * $b22 + $a13 * $b32;
        $m3[7]  = $a10 * $b03 + $a11 * $b13 + $a12 * $b23 + $a13 * $b33;
        $m3[8]  = $a20 * $b00 + $a21 * $b10 + $a22 * $b20 + $a23 * $b30;
        $m3[9]  = $a20 * $b01 + $a21 * $b11 + $a22 * $b21 + $a23 * $b31;
        $m3[10] = $a20 * $b02 + $a21 * $b12 + $a22 * $b22 + $a23 * $b32;
        $m3[11] = $a20 * $b03 + $a21 * $b13 + $a22 * $b23 + $a23 * $b33;
        $m3[12] = $a30 * $b00 + $a31 * $b10 + $a32 * $b20 + $a33 * $b30;
        $m3[13] = $a30 * $b01 + $a31 * $b11 + $a32 * $b21 + $a33 * $b31;
        $m3[14] = $a30 * $b02 + $a31 * $b12 + $a32 * $b22 + $a33 * $b32;
        $m3[15] = $a30 * $b03 + $a31 * $b13 + $a32 * $b23 + $a33 * $b33;
        return $m3;
    }

    /**
    * Prepends m2 to m1
    *
    * $m = Matrix4::prependMatrix($m1, $m2);
    */
    static function prependMatrix($m2, $m1)
    {
        $a00 = $m1[0];  $a01 = $m1[1];  $a02 = $m1[2];  $a03 = $m1[3];
        $a10 = $m1[4];  $a11 = $m1[5];  $a12 = $m1[6];  $a13 = $m1[7];
        $a20 = $m1[8];  $a21 = $m1[9];  $a22 = $m1[10]; $a23 = $m1[11];
        $a30 = $m1[12]; $a31 = $m1[13]; $a32 = $m1[14]; $a33 = $m1[15];
        $b00 = $m2[0];  $b01 = $m2[1];  $b02 = $m2[2];  $b03 = $m2[3];
        $b10 = $m2[4];  $b11 = $m2[5];  $b12 = $m2[6];  $b13 = $m2[7];
        $b20 = $m2[8];  $b21 = $m2[9];  $b22 = $m2[10]; $b23 = $m2[11];
        $b30 = $m2[12]; $b31 = $m2[13]; $b32 = $m2[14]; $b33 = $m2[15];
        $m3 = [];
        $m3[0]  = $a00 * $b00 + $a01 * $b10 + $a02 * $b20 + $a03 * $b30;
        $m3[1]  = $a00 * $b01 + $a01 * $b11 + $a02 * $b21 + $a03 * $b31;
        $m3[2]  = $a00 * $b02 + $a01 * $b12 + $a02 * $b22 + $a03 * $b32;
        $m3[3]  = $a00 * $b03 + $a01 * $b13 + $a02 * $b23 + $a03 * $b33;
        $m3[4]  = $a10 * $b00 + $a11 * $b10 + $a12 * $b20 + $a13 * $b30;
        $m3[5]  = $a10 * $b01 + $a11 * $b11 + $a12 * $b21 + $a13 * $b31;
        $m3[6]  = $a10 * $b02 + $a11 * $b12 + $a12 * $b22 + $a13 * $b32;
        $m3[7]  = $a10 * $b03 + $a11 * $b13 + $a12 * $b23 + $a13 * $b33;
        $m3[8]  = $a20 * $b00 + $a21 * $b10 + $a22 * $b20 + $a23 * $b30;
        $m3[9]  = $a20 * $b01 + $a21 * $b11 + $a22 * $b21 + $a23 * $b31;
        $m3[10] = $a20 * $b02 + $a21 * $b12 + $a22 * $b22 + $a23 * $b32;
        $m3[11] = $a20 * $b03 + $a21 * $b13 + $a22 * $b23 + $a23 * $b33;
        $m3[12] = $a30 * $b00 + $a31 * $b10 + $a32 * $b20 + $a33 * $b30;
        $m3[13] = $a30 * $b01 + $a31 * $b11 + $a32 * $b21 + $a33 * $b31;
        $m3[14] = $a30 * $b02 + $a31 * $b12 + $a32 * $b22 + $a33 * $b32;
        $m3[15] = $a30 * $b03 + $a31 * $b13 + $a32 * $b23 + $a33 * $b33;
        return $m3;
    }

    /**
    * Creates a frustum matrix.
    *
    * $m = Matrix4::frustum($left, $right, $bottom, $top, $near, $far);
    */
    static function frustum($left, $right, $bottom, $top, $near, $far)
    {
        $temp1 = 2 * $near;
        $temp2 = $right - $left;
        $temp3 = $top - $bottom;
        $temp4 = $far - $near;
        $m = [];
        $m[0]  = $temp1 / $temp2;
        $m[1]  = 0;
        $m[2]  = 0;
        $m[3]  = 0;
        $m[4]  = 0;
        $m[5]  = $temp1 / $temp3;
        $m[6]  = 0;
        $m[7]  = 0;
        $m[8]  = ($right + $left) / $temp2;
        $m[9]  = ($top + $bottom) / $temp3;
        $m[10] = (-$far - $near) / $temp4;
        $m[11] = -1;
        $m[12] = 0;
        $m[13] = 0;
        $m[14] = (-$temp1 * $far) / $temp4;
        $m[15] = 0;
        return $m;
    }

    /**
    * Builds a field of view projection matrix.
    *
    * $mProj = Matrix4::perspectiveFovLH($fovY, $aspect, $near, $far);
    */
    static function perspectiveFovLH($fovY, $aspect, $near, $far)
    {
        // https://github.com/thetinyspark/tomahawk/blob/master/tomahawk/core/geom/Matrix4x4.js
//$top = $near * tan($fovY*0.5);
//$right = $top * $aspect;
//return self::frustum(-$right, $right, -$top, $top, $near, $far);
        // https://msdn.microsoft.com/de-de/library/windows/desktop/bb281727%28v=vs.85%29.aspx
        $yScale = cot($fovY*0.5);
        $xScale = $yScale / $aspect;
        return [
            $xScale, 0, 0, 0,
            0, $yScale, 0, 0,
            0, 0, $far/($far-$near), 1,
            0, 0, $near*$far/($far-$near), 0
        ];
    }

    /**
    * Builds a left-handed look-at matrix.
    *
    * $mView = Matrix4::lookAtLH($camera, $target, $up);
    */
    static function lookAtLH($camera, $target, $up)
    {
        $z = Vector3::normalize((Vector3::subtract($target, $camera)));
        $x = Vector3::normalize((Vector3::cross($up, $z)));
        $y = Vector3::cross($z, $x);
        return [
            $x[0], $x[1], $x[2], -Vector3::dot($x,$camera),
            $y[0], $y[1], $y[2], -Vector3::dot($y,$camera),
            $z[0], $z[1], $z[2], -Vector3::dot($z,$camera),
            0, 0, 0, 1
        ];
    }

    /**
    * Builds a right-handed look-at matrix.
    *
    * $mView = Matrix4::lookAtRH($camera, $target, $up);
    */
    static function lookAtRH($camera, $target, $up)
    {
        $z = Vector3::normalize((Vector3::subtract($camera, $target)));
        $x = Vector3::normalize((Vector3::cross($up, $z)));
        $y = Vector3::cross($z, $x);
        return [
            $x[0], $x[1], $x[2], -Vector3::dot($x,$camera),
            $y[0], $y[1], $y[2], -Vector3::dot($y,$camera),
            $z[0], $z[1], $z[2], -Vector3::dot($z,$camera),
            0, 0, 0, 1
        ];
    }

    /**
    * Builds a matrix with a specified yaw, pitch, and roll (Tait-Bryan angle ZYX).
    *
    * $ypr = Matrix4::rotationYawPitchRoll($z, $y, $x);
    *
    * http://planning.cs.uiuc.edu/node102.html
    * https://de.wikipedia.org/wiki/Roll-Nick-Gier-Winkel
    */
    static function rotationYawPitchRoll($radZ, $radY, $radX)
    {
        $cosZ = cos($radZ);
        $sinZ = sin($radZ);
        $cosY = cos($radY);
        $sinY = sin($radY);
        $cosX = cos($radX);
        $sinX = sin($radX);
        return [
            ($cosZ*$cosY), ($cosZ*$sinY*$sinX-$sinZ*$cosX), ($cosZ*$sinY*$cosX+$sinZ*$sinX), 0,
            ($sinZ*$cosY), ($sinZ*$sinY*$sinX+$cosZ*$cosX), ($sinZ*$sinY*$cosX-$cosZ*$sinX), 0,
            (-$sinY)     , ($cosY*$sinX)                  , ($cosY*$cosX)                  , 0,
            0            , 0                              , 0                              , 1
        ];
    }








}
