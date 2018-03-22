<?php

class Speedtest
{
    private function __construct() {}

    static private function loop($loops, $func)
    {
        $tStart = microtime(true);
        while($loops--) $func();
        $tEnd = microtime(true);
        return $tEnd - $tStart;
    }

    static public function test($loops, $funcs)
    {
        $selfTime = self::loop($loops, function(){});
        $usedTime = [];
        foreach($funcs as $func) $usedTime[] = self::loop($loops, $func) - $selfTime;
        return $usedTime;
    }

}
