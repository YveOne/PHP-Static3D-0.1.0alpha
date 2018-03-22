<?php

class Plane
{
    private function __construct(){}

    static public function fromTriangle($v1, $v2, $v3)
    {
        $n = Vector3::cross(
            (Vector3::subtract($v2, $v1)),
            (Vector3::subtract($v3, $v1))
        );
        $d = -Vector3::dot($n, $v1);

print_r($v1); echo "<br>";
print_r($v2); echo "<br>";
print_r($v3); echo "<br>";
print_r($n); echo "<br>";
print_r($d); echo "<br>";
die();
        return [$n, $d];
    }

}
