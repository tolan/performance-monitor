<?php
include 'Performance/Profiler/Monitor.php';
\PF\Profiler\Monitor::getInstance()->enable();
declare(ticks=1);

    $ans = simple(10);
    echo $ans;

    (new Foo())->bar(20);

\PF\Profiler\Monitor::getInstance()->disable()->display();

function simple ($input) {
    for($i = 0; $i < 3; $i++) {
        $input = sub($input, $i);
        $time = time();
    }

    return $input;
}

function sub ($a, $b) {
    return $a - $b;
}

class Foo {

    public function bar($input) {
        echo '<div>Foo->bar('.$input.'): '.simple($input).'</div>';
    }
}