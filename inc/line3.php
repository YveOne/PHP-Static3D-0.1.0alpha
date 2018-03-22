<?php

//TODO: docu


/*
$origin = [$x, $y, $z];
$direction = [$x, $y, $z];
$line3 = [$origin, $direction];
*/

// namespace S3DGeometry

class Line3
{
    private function __construct(){}




    // WTF IS THIS FUNC FOR ???
    static function nearest($line, $v)
    {
        $o = $line[0];
        $d = $line[1];
        return Vector3::sum(
            $o,
            Vector3::scale(
                $d,
                Vector3::dot(
                    $d,
                    (Vector3::subtract($v, $o))
                ) / Vector3::dot($d, $d)
            )
        );
    }








}
