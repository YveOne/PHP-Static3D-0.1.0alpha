<?php

//TODO: docu


//line2Line2
//line3Line3


class Intersection
{
    private function __construct(){}













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



    static function planePlane(&$red, $blu)
    {
        $redN = &$red[0];
        $redD = &$red[1];
        $bluN = &$blu[0];
        $bluD = &$blu[1];
        $lv = Vector3::normalize((Vector3::cross($redN, $bluN)));
        $nd = Vector3::scaleNum($redN, -$redD);
        $o = Vector3::subtractLR($nd, (Vector3::scaleNum($redN, Vector3::dot($redN, $nd)+$redD)));
        $d = Vector3::cross($redN, $lv);
        return [
            Vector3::addVec($o, (Vector3::scaleNum($d, (-distance($bluN, $bluD, $o) / Vector3::dot($bluN, $d))))),
            &$lv
        ];
    }

















}
