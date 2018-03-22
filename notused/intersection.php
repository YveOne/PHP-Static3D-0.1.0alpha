<?php

//TODO: docu


//line2Line2
//line3Line3


class Intersection
{
    private function __construct(){}





    static function line2Line2(&$eq1, &$eq2)
    {
        // both got same slope (this also prevents division by zero)
        if($eq1[0] == $eq2[0]) return false;
        // m1*x1 +n1 = m2*x2 + n2
        $x = ($eq1[1] - $eq2[1]) / ($eq2[0] - $eq1[0]);
        return [
            $x,
            $eq1[0] * $x + $eq1[1]
        ];



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









    /*
    $l = [[1,1,1], [1,1,1]];
    $r = [[-2,-3,-4], [4,5,6]];
    if(Line3::isParallel($l, $r)) die("parallel");
    $i = Intersection::line3Line3($l, $r);
    print_r($i); die();
    //https://de.mathworks.com/matlabcentral/newsreader/view_thread/246420
    */
    static function line3Line3(&$l, &$r)
    {
        $lPos = &$l[0];
        $lDir = &$l[1];
        $rPos = &$r[0];
        $rDir = &$r[1];
        $A  = Vector3::subtractLR($lPos, $rPos);
        $LR = Vector3::cross($lDir, $rDir);
        $d  = Vector3::dot($LR, $LR);
        $rN = Vector3::dot(Vector3::cross($lDir, $A), $LR) / $d;
        return [
            $r[0][0] + $rN*$r[1][0],
            $r[0][1] + $rN*$r[1][1],
            $r[0][2] + $rN*$r[1][2]
        ];
        $lN = Vector3::dot(Vector3::cross($rDir, $A), $LR) / $d;
        return [
            $l[0][0] + $lN*$l[1][0],
            $l[0][1] + $lN*$l[1][1],
            $l[0][2] + $lN*$l[1][2]
        ];
    }







}
