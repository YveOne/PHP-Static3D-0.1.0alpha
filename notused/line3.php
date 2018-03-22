<?php

//TODO: docu

class Line3
{
    private function __construct(){}

    /**
    * Returns Line3 object.
    *
    * $line3 = Line3::line3FromTo($from, $to);
    */
    static function line3FromTo(&$from, &$to)
    {
        return [
            $from,
            [
                $to[0] - $from[0],
                $to[1] - $from[1],
                $to[2] - $from[2]
            ]
        ];
    }



    static function isParallel(&$l, &$r)
    {
        $ld = &$l[1];
        $rd = &$r[1];
        $x = $ld[0] - $rd[0];
        $y = $ld[1] - $rd[1];
        $z = $ld[2] - $rd[2];
        if($x != $y) return false;
        if($y != $z) return false;
        return true;
    }







}
