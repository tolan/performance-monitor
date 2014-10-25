<?php
include 'Performance/Profiler/Monitor.php';
\PM\Profiler\Monitor::getInstance()->enable();
declare(ticks=1);

    $ans = simple(10);
    echo $ans;

    (new Foo())->bar(20);

    test_if(true);
    test_if(false);

    sleep(3);
\PM\Profiler\Monitor::getInstance()->disable()->display();

function simple ($input) {
    for($i = 0; $i < 2; $i++) {
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

    public function test() {
        usleep(10000);
        class_exists('Foo');
    }
}

function test_if ($cond) {
    if ($cond == true) {
        $ref = new ReflectionMethod('Foo', 'test');
        $ref->invokeArgs(new Foo(), array());
    }

}
