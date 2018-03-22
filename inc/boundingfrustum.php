<?php

//TODO: docu

//http://www.technologicalutopia.com/sourcecode/xnageometry/boundingfrustum.cs.htm

/*
$bf = [
    //planes (list of Plane)
    0 => [
        0 => $planeLeft
        1 => $planeRight
        2 => $planeTop
        3 => $planeBottom
        4 => $planeNear
        5 => $planeFar
    ]
    //corners (list of Vector3)
    1 => [
        0 => $cornerNearLeftTop
        1 => $cornerNearRightTop
        2 => $cornerNearRightBottom
        3 => $cornerNearLeftBottom
        4 => $cornerFarLeftTop
        5 => $cornerFarRightTop
        6 => $cornerFarRightBottom
        7 => $cornerFarLeftBottom
    ]
]
*/

// namespace S3DGeometry

class BoundingFrustum
{
    private function __construct(){}

    static function fromMatrix($m)
    {
        $p = self::createPlanes($m);
        $c = self::createCorners($p);
        return [$p, $c];
    }

    static function CreatePlanes($m)
    {
        $p = [];

        $m00 = $m[0]; $m01 = $m[1]; $m02 = $m[2]; $m03 = $m[3];
        $m10 = $m[4]; $m11 = $m[5]; $m12 = $m[6]; $m13 = $m[7];
        $m20 = $m[8]; $m21 = $m[9]; $m22 = $m[10]; $m23 = $m[11];
        $m30 = $m[12]; $m31 = $m[13]; $m32 = $m[14]; $m33 = $m[15];

        $p[0] = [ // left
            [
                -$m03 - $m00,
                -$m13 - $m10,
                -$m23 - $m20
            ],
            -$m33 - $m30
        ];

        $p[1] = [ // right
            [
                $m00 - $m03,
                $m10 - $m13,
                $m20 - $m23
            ],
            $m30 - $m33
        ];

        $p[2] = [ // top
            [
                $m01 - $m03,
                $m11 - $m13,
                $m21 - $m23
            ],
            $m31 - $m33
        ];

        $p[3] = [ // bottom
            [
                -$m03 - $m01,
                -$m13 - $m11,
                -$m23 - $m21
            ],
            -$m33 - $m31
        ];

        $p[4] = [ // near
            [
                -$m02,
                -$m12,
                -$m22
            ],
            -$m32
        ];

        $p[5] = [ // far
            [
                $m02 - $m03,
                $m12 - $m13,
                $m22 - $m23
            ],
            $m32 - $m33
        ];

        $p[0] = self::normalizePlane($p[0]);
        $p[1] = self::normalizePlane($p[1]);
        $p[2] = self::normalizePlane($p[2]);
        $p[3] = self::normalizePlane($p[3]);
        $p[4] = self::normalizePlane($p[4]);
        $p[5] = self::normalizePlane($p[5]);

        return $p;
    }

    static function createCorners(&$p)
    {
        $c = [];
        $c[0] = self::intersectionPoint($p[4], $p[0], $p[2]);
        $c[1] = self::intersectionPoint($p[4], $p[1], $p[2]);
        $c[2] = self::intersectionPoint($p[4], $p[1], $p[3]);
        $c[3] = self::intersectionPoint($p[4], $p[0], $p[3]);
        $c[4] = self::intersectionPoint($p[5], $p[0], $p[2]);
        $c[5] = self::intersectionPoint($p[5], $p[1], $p[2]);
        $c[6] = self::intersectionPoint($p[5], $p[1], $p[3]);
        $c[7] = self::intersectionPoint($p[5], $p[0], $p[3]);
        return $c;
    }


    static function normalizePlane(&$p)
    {
    //http://www.technologicalutopia.com/sourcecode/xnageometry/boundingfrustum.cs.htm
        $factor = 1/Vector3::length($p[0]);
        return[
            [
                $p[0][0]*$factor,
                $p[0][1]*$factor,
                $p[0][2]*$factor
            ],
            $p[1]*$factor
        ];
    }

    static function intersectionPoint(&$a, &$b, &$c)
    {
        //http://www.technologicalutopia.com/sourcecode/xnageometry/boundingfrustum.cs.htm
        // Formula used
        //                d1 ( N2 * N3 ) + d2 ( N3 * N1 ) + d3 ( N1 * N2 )
        //P =     -------------------------------------------------------------------------
        //                             N1 . ( N2 * N3 )
        //
        // Note: N refers to the normal, d refers to the displacement. '.' means dot product. '*' means cross product
        $f  =  Vector3::dot($a[0], (Vector3::cross($b[0], $c[0])));
        $v1 = Vector3::scale((Vector3::cross($b[0], $c[0])), $a[1]);
        $v2 = Vector3::scale((Vector3::cross($c[0], $a[0])), $b[1]);
        $v3 = Vector3::scale((Vector3::cross($a[0], $b[0])), $c[1]);
        return [
            ($v1[0] + $v2[0] + $v3[0]) / $f,
            ($v1[1] + $v2[1] + $v3[1]) / $f,
            ($v1[2] + $v2[2] + $v3[2]) / $f
        ];
    }





}
