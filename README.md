# Performance Framework
Welcome to the project, which aims to create PHP Performance framework.
The purpose of the framework is the analysis, optimization, control and evaluate the performance of code in PHP project.

## Requirements
* Apache:  2.2
* PHP:     5.3+
* MySQL:   5.5
* Gearman: 1.1.2

## Configuration
- configuration is in file: Performance/config.php
- there is simple array with name $config
- look to part database for settings of MySQL
- when you run application first time then set 'database' => 'install' to TRUE. It is for create all tables.


## Run example
- for measure you must start worker server. Run file startWorker.php in Performance/Main without parameters.
- include this code to your project:
<pre>
  include 'Performance/Profiler/Monitor.php';
  Performance_Profiler_Monitor::getInstance()->enable();
  declare(ticks=1);

  /** Your code **/
  
  Performance_Profiler_Monitor::getInstance()->disable();
</pre>
- next go to page http://__your-domain__/Performance/web/
