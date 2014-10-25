# Performance Monitor
Welcome to the project, which aims to create PHP Performance monitor.
The purpose of the monitor is the analysis, optimization, control and evaluate the performance of code in PHP project.

## Requirements
* Apache:        2.2
* PHP:           5.4
* MySQL:         5.5
* Gearman:       1.1.2
* HTTP_Request2: 2.1.1 (see: http://pear.php.net/package/HTTP_Request2)
* (YAML: 1.1.1 (from PECL) - only for unit tests)

## Configuration
- configuration is in file: Performance/config.json
- there is simple object with config
- look to part database for settings of MySQL
- when you run application first time then set 'database' => 'install' to TRUE. It is for create all tables and translation.


## Run example
- for measure you must start gearman server. Run file startWorker.php in Performance/Main without parameters.
- include this code to your project (see to example.php):
<pre>
  include 'Performance/Profiler/Monitor.php';
  \PM\Profiler\Monitor::getInstance()->enable();
  declare(ticks=1);

  /** Your code **/

  \PM\Profiler\Monitor::getInstance()->disable()->display(); // This line is optional. If you remove it then it is called in destruct.
</pre>
- next go to page http://__your-domain__/Performance/web/

## Version info
- version: 0.8
