<?php

//TODO: docu

class Line2
{
    private function __construct(){}

    // horizontal/diagonal:
    // y = m*x + n
    // vertical (m==0):
    // x = n
    // $eq = [$m, $n]
    static function equation(&$pos, &$dir)
    {
        $eq = [0, $pos[0]];
        //if ($direction->x == 0) return $this->n = $position->x;
        if($dir[0] == 0) return $eq;
        $eq[0] = $dir[1] / $dir[0];
        $eq[1] = $pos[1] - ($eq[0] * $pos[0]);
        return $eq;
    }





}
