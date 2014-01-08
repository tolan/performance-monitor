<?php
include 'Performance/Profiler/Monitor.php';
\PF\Profiler\Monitor::getInstance()->enable();
declare(ticks=1);

echo "hello";

a();

sleep(1);

\PF\Profiler\Monitor::getInstance()->disable();


function a() {
    for($i = 0; $i < 4; $i++) {
        b();
    }

    $arr = array(1,2);
    $test = 0;

    foreach ($arr as $a) {
        while($test < 1) {
            $test++;
        }

        echo $a;
    }
}

function b() {
    echo 'start b';
    usleep(20000);
    c();
    usleep(20000);
    echo 'end b';
}

function c() {
    echo 'c';
    usleep(50000);
}
