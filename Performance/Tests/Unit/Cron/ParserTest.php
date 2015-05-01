<?php

namespace PM\Tests\Unit\Cron;

use PM\Main\Abstracts\Unit\TestCase;
use PM\Cron\Parser;
use PM\Cron\Parser\Date;

/**
 * This script defines class for php unit test case of class \PM\Cron\Parser.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class ParserTest extends TestCase {

    /**
     * Parser instance.
     *
     * @var Parser
     */
    private $_parser;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_parser = $this->getProvider()->get('PM\Cron\Parser');
    }

    /**
     * Success test for next time of simple expression.
     *
     * @return void
     */
    public function testNextSimpleAll() {
        $expression = '* * * * *';

        $parser = $this->_prepareParser($expression, '2015-03-15 22:12');
        $this->assertEquals('2015-03-15 22:13', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2000-08-01 15:15');
        $this->assertEquals('2000-08-01 15:16', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2050-11-20 01:00');
        $this->assertEquals('2050-11-20 01:01', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2011-01-01 01:59');
        $this->assertEquals('2011-01-01 02:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2011-01-01 23:59');
        $this->assertEquals('2011-01-02 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2011-02-28 23:59');
        $this->assertEquals('2011-03-01 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2011-12-31 23:59');
        $this->assertEquals('2012-01-01 00:00', $parser->resolveNext()->format('Y-m-d H:i'));
    }

    /**
     * Success test for next time of expression with spceific minute.
     *
     * @return void
     */
    public function testNextMinute() {
        $parser = $this->_prepareParser('*/5 * * * *', '2015-03-15 22:12');
        $this->assertEquals('2015-03-15 22:15', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('*/5,*/3 * * * *', '2015-03-15 22:11');
        $this->assertEquals('2015-03-15 22:12', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('*/5,*/3 * * * *', '2015-03-15 22:24');
        $this->assertEquals('2015-03-15 22:25', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('10,16,24 * * * *', '2015-03-15 22:12');
        $this->assertEquals('2015-03-15 22:16', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('5-8,*/5 * * * *', '2015-03-15 22:12');
        $this->assertEquals('2015-03-15 22:15', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('5-8,*/5 * * * *', '2015-03-15 22:07');
        $this->assertEquals('2015-03-15 22:08', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('*/5 * * * *', '2015-03-15 22:56');
        $this->assertEquals('2015-03-15 23:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('0 * * * *', '2015-03-15 23:12');
        $this->assertEquals('2015-03-16 00:00', $parser->resolveNext()->format('Y-m-d H:i'));
    }

    /**
     * Success test for next time of expression with spceific hour.
     *
     * @return void
     */
    public function testNextHour() {
        $parser = $this->_prepareParser('* */5 * * *', '2015-03-15 22:12');
        $this->assertEquals('2015-03-16 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* */5,*/3 * * *', '2015-03-15 15:11');
        $this->assertEquals('2015-03-15 15:12', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* */5,*/3 * * *', '2015-03-15 16:24');
        $this->assertEquals('2015-03-15 18:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* */5,*/3 * * *', '2015-03-15 18:59');
        $this->assertEquals('2015-03-15 20:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 10,16 * * *', '2015-03-15 22:12');
        $this->assertEquals('2015-03-16 10:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 10,16 * * *', '2015-03-15 10:59');
        $this->assertEquals('2015-03-15 16:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 5-8,*/5 * * *', '2015-03-15 20:12');
        $this->assertEquals('2015-03-15 20:13', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 5-8,*/5 * * *', '2015-03-15 01:12');
        $this->assertEquals('2015-03-15 05:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 5-8,*/5 * * *', '2015-03-15 06:12');
        $this->assertEquals('2015-03-15 06:13', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* */5 * * *', '2015-03-15 22:56');
        $this->assertEquals('2015-03-16 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* */5 * * *', '2015-03-15 20:56');
        $this->assertEquals('2015-03-15 20:57', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* */5 * * *', '2015-03-15 12:00');
        $this->assertEquals('2015-03-15 15:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 0 * * *', '2015-03-15 23:12');
        $this->assertEquals('2015-03-16 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 0 * * *', '2015-03-15 00:22');
        $this->assertEquals('2015-03-15 00:23', $parser->resolveNext()->format('Y-m-d H:i'));
    }

    /**
     * Success test for next time of expression with spceific day.
     *
     * @return void
     */
    public function testNextDay() {
        $parser = $this->_prepareParser('* * 11 * *', '2015-03-15 23:12');
        $this->assertEquals('2015-04-11 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 11 * *', '2015-03-11 23:12');
        $this->assertEquals('2015-03-11 23:13', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 11 * *', '2015-03-05 23:12');
        $this->assertEquals('2015-03-11 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * */10 * *', '2015-03-05 23:12');
        $this->assertEquals('2015-03-10 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * */10 * *', '2015-03-10 23:12');
        $this->assertEquals('2015-03-10 23:13', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * */10 * *', '2015-03-10 23:59');
        $this->assertEquals('2015-03-20 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 31 * *', '2015-04-10 01:30');
        $this->assertEquals('2015-05-31 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 10,31 * *', '2015-04-15 01:30');
        $this->assertEquals('2015-05-10 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 1-15 * *', '2015-04-10 01:30');
        $this->assertEquals('2015-04-10 01:31', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 1-15 * *', '2015-04-16 01:30');
        $this->assertEquals('2015-05-01 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 1-10,*/5 * *', '2015-04-16 01:30');
        $this->assertEquals('2015-04-20 00:00', $parser->resolveNext()->format('Y-m-d H:i'));
    }

    /**
     * Success test for next time of expression with spceific month.
     *
     * @return void
     */
    public function testNextMonth() {
        $parser = $this->_prepareParser('* * * 11 *', '2015-03-15 23:12');
        $this->assertEquals('2015-11-01 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * 11 *', '2015-11-15 15:10');
        $this->assertEquals('2015-11-15 15:11', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * 11 *', '2015-11-30 23:59');
        $this->assertEquals('2016-11-01 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * */3 *', '2015-11-30 23:59');
        $this->assertEquals('2015-12-01 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * */3 *', '2015-12-31 23:59');
        $this->assertEquals('2016-03-01 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * */3 *', '2015-12-15 11:59');
        $this->assertEquals('2015-12-15 12:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * 1,2,*/3,5-7 *', '2015-04-15 11:59');
        $this->assertEquals('2015-05-01 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * 1,2,*/3,5-7 *', '2015-07-12 10:05');
        $this->assertEquals('2015-07-12 10:06', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * 1,2,*/3,5-7 *', '2015-12-31 23:59');
        $this->assertEquals('2016-01-01 00:00', $parser->resolveNext()->format('Y-m-d H:i'));
    }

    /**
     * Success test for next time of expression with spceific day of week.
     *
     * @return void
     */
    public function testNextDayOfWeek() {
        $parser = $this->_prepareParser('* * * * 1', '2015-01-01 23:12');
        $this->assertEquals('2015-01-05 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * * 1', '2015-01-06 23:12');
        $this->assertEquals('2015-01-12 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * * 1', '2015-01-30 23:12');
        $this->assertEquals('2015-02-02 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * * */3', '2015-01-01 23:12');
        $this->assertEquals('2015-01-03 00:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * * */3', '2015-01-07 23:12');
        $this->assertEquals('2015-01-07 23:13', $parser->resolveNext()->format('Y-m-d H:i'));
    }

    /**
     * Success test for next time of complex expression.
     *
     * @return void
     */
    public function testNextComplex() {
        $expression = '*/4 6,11 5-31/5 2,3 *';

        $parser = $this->_prepareParser($expression, '2015-03-15 22:12');
        $this->assertEquals('2015-03-20 06:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2015-03-31 11:59');
        $this->assertEquals('2016-02-05 06:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2015-02-05 11:13');
        $this->assertEquals('2015-02-05 11:16', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2015-03-15 22:12');
        $this->assertEquals('2015-03-20 06:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2015-11-11 11:11');
        $this->assertEquals('2016-02-05 06:00', $parser->resolveNext()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2015-03-01 00:00');
        $this->assertEquals('2015-03-05 06:00', $parser->resolveNext()->format('Y-m-d H:i'));
    }

    /**
     * Success test for check that time is actual for simple expression.
     *
     * @return void
     */
    public function testIsActualSimpleAll() {
        $parser = $this->_prepareParser('* * * * *', '2015-02-02 12:41');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * * *', '2016-02-28 15:01');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * * *', '2014-11-11 11:11');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());
    }

    /**
     * Success test for check that time is actual for expression with specific minute.
     *
     * @return void
     */
    public function testIsActualMinute() {
        $parser = $this->_prepareParser('*/5 * * * *', '2015-03-15 22:15');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('*/5 * * * *', '2015-03-15 22:12');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('*/5,*/3 * * * *', '2015-03-15 22:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('*/5,*/3 * * * *', '2015-03-15 22:11');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('10,16,24 * * * *', '2015-03-15 22:16');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('10,16,24 * * * *', '2015-03-15 22:30');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('5-8,*/5 * * * *', '2015-03-15 22:07');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('5-8,*/5 * * * *', '2015-03-15 22:20');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('5-8,*/5 * * * *', '2015-03-15 22:32');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('0 * * * *', '2015-03-15 23:00');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('0 * * * *', '2015-03-15 23:15');
        $this->assertNull($parser->resolveIsActual());
    }

    /**
     * Success test for check that time is actual for expression with specific hour.
     *
     * @return void
     */
    public function testIsActualHour() {
        $parser = $this->_prepareParser('* */5 * * *', '2015-03-15 20:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* */5 * * *', '2015-03-15 21:12');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('* */5,*/3 * * *', '2015-03-15 15:11');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* */5,*/3 * * *', '2015-03-15 18:24');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* */5,*/3 * * *', '2015-03-15 11:24');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('* 10,16 * * *', '2015-03-15 10:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* 10,16 * * *', '2015-03-15 16:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* 10,16 * * *', '2015-03-15 11:59');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('* 5-8,*/5 * * *', '2015-03-15 20:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* 5-8,*/5 * * *', '2015-03-15 06:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* 5-8,*/5 * * *', '2015-03-15 04:12');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('* 0 * * *', '2015-03-15 00:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* 0 * * *', '2015-03-15 22:22');
        $this->assertNull($parser->resolveIsActual());
    }

    /**
     * Success test for check that time is actual for expression with specific day.
     *
     * @return void
     */
    public function testIsActualDay() {
        $parser = $this->_prepareParser('* * 11 * *', '2015-03-11 23:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * 11 * *', '2015-03-15 23:12');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('* * */10 * *', '2015-03-10 23:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * */10 * *', '2015-03-12 23:12');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('* * 1-15 * *', '2015-04-10 01:30');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * 1-15 * *', '2015-04-20 01:30');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('* * 1-10,*/5 * *', '2015-04-06 01:30');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * 1-10,*/5 * *', '2015-04-20 01:30');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * 1-10,*/5 * *', '2015-04-23 01:30');
        $this->assertNull($parser->resolveIsActual());
    }

    /**
     * Success test for check that time is actual for expression with specific month.
     *
     * @return void
     */
    public function testIsActualMonth() {
        $parser = $this->_prepareParser('* * * 11 *', '2015-11-15 23:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * 11 *', '2015-05-15 23:12');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * */3 *', '2015-09-30 23:59');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * */3 *', '2015-10-30 23:59');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * 1,2,*/3,5-7 *', '2015-02-15 11:59');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * 1,2,*/3,5-7 *', '2015-09-15 11:59');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * 1,2,*/3,5-7 *', '2015-07-15 11:59');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * 1,2,*/3,5-7 *', '2015-10-15 11:59');
        $this->assertNull($parser->resolveIsActual());
    }

    /**
     * Success test for check that time is actual for expression with specific day of week.
     *
     * @return void
     */
    public function testIsActualDayOfWeek() {
        $parser = $this->_prepareParser('* * * * 1', '2015-01-05 23:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * * 1', '2015-01-03 23:12');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * * */3', '2015-01-07 23:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * * */3', '2015-01-06 23:12');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * * 1-2,*/3', '2015-01-05 23:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * * 1-2,*/3', '2015-01-07 23:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser('* * * * 1-2,*/3', '2015-01-09 23:12');
        $this->assertNull($parser->resolveIsActual());
    }

    /**
     * Success test for check that time is actual for complex expression.
     *
     * @return void
     */
    public function testIsActualComplex() {
        $expression = '*/4 6,11 5-31/5 2,3 *';

        $parser = $this->_prepareParser($expression, '2015-03-15 11:12');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser($expression, '2015-02-20 06:20');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser($expression, '2015-03-05 11:20');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser($expression, '2015-02-10 06:32');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser($expression, '2015-02-15 11:16');
        $this->assertInstanceOf('PM\Cron\Parser\Date', $parser->resolveIsActual());

        $parser = $this->_prepareParser($expression, '2015-03-15 11:11');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser($expression, '2015-03-15 12:12');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser($expression, '2015-03-16 11:12');
        $this->assertNull($parser->resolveIsActual());

        $parser = $this->_prepareParser($expression, '2015-04-15 11:12');
        $this->assertNull($parser->resolveIsActual());
    }

    /**
     * Success test for before time of simple expression.
     *
     * @return void
     */
    public function testBeforeSimpleAll() {
        $expression = '* * * * *';

        $parser = $this->_prepareParser($expression, '2015-03-15 22:12');
        $this->assertEquals('2015-03-15 22:11', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2000-08-01 15:15');
        $this->assertEquals('2000-08-01 15:14', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2050-11-20 01:00');
        $this->assertEquals('2050-11-20 00:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2011-01-01 01:59');
        $this->assertEquals('2011-01-01 01:58', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2011-01-01 00:00');
        $this->assertEquals('2010-12-31 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2011-03-01 00:00');
        $this->assertEquals('2011-02-28 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2012-03-01 00:00');
        $this->assertEquals('2012-02-29 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));
    }

    /**
     * Success test for before time of expression with spceific minute.
     *
     * @return void
     */
    public function testBeforeMinute() {
        $parser = $this->_prepareParser('*/5 * * * *', '2015-03-15 22:12');
        $this->assertEquals('2015-03-15 22:10', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('*/5,*/3 * * * *', '2015-03-15 22:11');
        $this->assertEquals('2015-03-15 22:10', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('*/5,*/3 * * * *', '2015-03-15 22:24');
        $this->assertEquals('2015-03-15 22:21', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('10,16,24 * * * *', '2015-03-15 22:12');
        $this->assertEquals('2015-03-15 22:10', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('5-8,*/5 * * * *', '2015-03-15 22:12');
        $this->assertEquals('2015-03-15 22:10', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('5-8,*/5 * * * *', '2015-03-15 22:07');
        $this->assertEquals('2015-03-15 22:06', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('*/5 * * * *', '2015-03-15 00:00');
        $this->assertEquals('2015-03-14 23:55', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('0 * * * *', '2015-03-15 23:12');
        $this->assertEquals('2015-03-15 23:00', $parser->resolveBefore()->format('Y-m-d H:i'));
    }

    /**
     * Success test for before time of expression with spceific hour.
     *
     * @return void
     */
    public function testBeforeHour() {
        $parser = $this->_prepareParser('* */5 * * *', '2015-03-15 00:00');
        $this->assertEquals('2015-03-14 20:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* */5,*/3 * * *', '2015-03-15 15:11');
        $this->assertEquals('2015-03-15 15:10', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* */5,*/3 * * *', '2015-03-15 16:24');
        $this->assertEquals('2015-03-15 15:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* */5,*/3 * * *', '2015-03-15 21:00');
        $this->assertEquals('2015-03-15 20:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 10,16 * * *', '2015-03-16 02:12');
        $this->assertEquals('2015-03-15 16:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 10,16 * * *', '2015-03-15 16:00');
        $this->assertEquals('2015-03-15 10:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 5-8,*/5 * * *', '2015-03-15 20:12');
        $this->assertEquals('2015-03-15 20:11', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 5-8,*/5 * * *', '2015-03-15 01:12');
        $this->assertEquals('2015-03-15 00:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 5-8,*/5 * * *', '2015-03-15 06:12');
        $this->assertEquals('2015-03-15 06:11', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* */5 * * *', '2015-03-16 00:00');
        $this->assertEquals('2015-03-15 20:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* */5 * * *', '2015-03-15 20:56');
        $this->assertEquals('2015-03-15 20:55', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* */5 * * *', '2015-03-15 12:00');
        $this->assertEquals('2015-03-15 10:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 0 * * *', '2015-03-15 23:12');
        $this->assertEquals('2015-03-15 00:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* 0 * * *', '2015-03-15 00:22');
        $this->assertEquals('2015-03-15 00:21', $parser->resolveBefore()->format('Y-m-d H:i'));
    }

    /**
     * Success test for before time of expression with spceific day.
     *
     * @return void
     */
    public function testBeforeDay() {
        $parser = $this->_prepareParser('* * 11 * *', '2015-03-15 23:12');
        $this->assertEquals('2015-03-11 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 11 * *', '2015-03-11 23:12');
        $this->assertEquals('2015-03-11 23:11', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 11 * *', '2015-03-05 23:12');
        $this->assertEquals('2015-02-11 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * */10 * *', '2015-03-05 23:12');
        $this->assertEquals('2015-02-20 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * */10 * *', '2015-03-10 23:12');
        $this->assertEquals('2015-03-10 23:11', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * */10 * *', '2015-03-10 00:00');
        $this->assertEquals('2015-02-20 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 31 * *', '2015-03-10 01:30');
        $this->assertEquals('2015-01-31 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 10,31 * *', '2015-04-05 01:30');
        $this->assertEquals('2015-03-31 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 1-15 * *', '2015-04-10 01:30');
        $this->assertEquals('2015-04-10 01:29', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 1-15 * *', '2015-04-01 00:00');
        $this->assertEquals('2015-03-15 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * 1-10,*/5 * *', '2015-04-16 01:30');
        $this->assertEquals('2015-04-15 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));
    }

    /**
     * Success test for before time of expression with spceific month.
     *
     * @return void
     */
    public function testBeforeMonth() {
        $parser = $this->_prepareParser('* * * 11 *', '2015-03-15 23:12');
        $this->assertEquals('2014-11-30 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * 11 *', '2015-11-15 15:10');
        $this->assertEquals('2015-11-15 15:09', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * 11 *', '2015-11-01 00:00');
        $this->assertEquals('2014-11-30 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * */3 *', '2015-10-01 00:00');
        $this->assertEquals('2015-09-30 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * */3 *', '2015-09-01 00:00');
        $this->assertEquals('2015-06-30 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * */3 *', '2015-12-15 11:59');
        $this->assertEquals('2015-12-15 11:58', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * 1,2,*/3,5-7 *', '2015-04-15 11:59');
        $this->assertEquals('2015-03-31 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * 1,2,*/3,5-7 *', '2015-07-12 10:05');
        $this->assertEquals('2015-07-12 10:04', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * 1,2,*/3,5-7 *', '2015-01-01 00:00');
        $this->assertEquals('2014-12-31 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));
    }

    /**
     * Success test for before time of expression with spceific day of week.
     *
     * @return void
     */
    public function testBeforeDayOfWeek() {
        $parser = $this->_prepareParser('* * * * 1', '2015-01-06 23:12');
        $this->assertEquals('2015-01-05 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * * 1', '2015-01-15 23:12');
        $this->assertEquals('2015-01-12 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * * 1', '2015-02-01 23:12');
        $this->assertEquals('2015-01-26 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * * */3', '2015-01-08 23:12');
        $this->assertEquals('2015-01-07 23:59', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser('* * * * */3', '2015-01-11 23:12');
        $this->assertEquals('2015-01-11 23:11', $parser->resolveBefore()->format('Y-m-d H:i'));
    }

    /**
     * Success test for before time of complext expression.
     *
     * @return void
     */
    public function testBeforeComplex() {
        $expression = '*/4 6,11 5-31/5 2,3 *';

        $parser = $this->_prepareParser($expression, '2015-03-15 22:12');
        $this->assertEquals('2015-03-15 11:56', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2015-05-11 12:31');
        $this->assertEquals('2015-03-30 11:56', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2015-01-15 22:12');
        $this->assertEquals('2014-03-30 11:56', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2015-02-05 06:01');
        $this->assertEquals('2015-02-05 06:00', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2015-03-02 22:12');
        $this->assertEquals('2015-02-25 11:56', $parser->resolveBefore()->format('Y-m-d H:i'));

        $parser = $this->_prepareParser($expression, '2015-02-15 11:54');
        $this->assertEquals('2015-02-15 11:52', $parser->resolveBefore()->format('Y-m-d H:i'));
    }

    /**
     * It prepares cron parser for test.
     *
     * @param string $expression Cron expression string
     * @param string $date       Date string
     *
     * @return Parser
     */
    private function _prepareParser($expression, $date) {
        $this->_parser
            ->setDatetime(new Date($date))
            ->setExpression(
                $this->_parser
                    ->getExpression()
                    ->setExpression($expression)
            );

        return $this->_parser;
    }
}
