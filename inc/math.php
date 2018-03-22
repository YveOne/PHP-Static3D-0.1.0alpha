<?php

function cot($rad)
{
    return cos($rad) / sin($rad);
}

function cot2($rad)
{
    return tan(M_PI_2 - rad2deg($rad));
}

/**
* Returns radian angle between two given points
*/
function angle($fromX, $fromY, $toX, $toY)
{
    $cos = $toX-$fromX;
    $sin = $toY-$fromY;
    $d = sqrt($cos*$cos + $sin*$sin);
    if ($d == 0) return 0;
    $cos /= $d;
    $sin /= $d;
    return ($sin >= 0) ? (acos($cos)) : (deg2rad(360) - (acos($cos)));
}

/**
* Function angle() uses yaw rotation
* This one returns pitch angle in 3d space
*/
function pitch($fromX, $fromY, $fromZ, $toX, $toY, $toZ)
{
      $xNull = $toX - $fromX;
      $yNull = $toY - $fromY;
      $xAlias = sqrt($xNull*$xNull + $yNull*$yNull);
      $yAlias = $toZ - $fromZ;
      return angle(0, 0, $xAlias, $yAlias);
}

