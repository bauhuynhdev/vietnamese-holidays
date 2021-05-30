<?php

namespace BauHuynh\VietnameseHolidays\Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use BauHuynh\VietnameseHolidays\Repository as VietnameseHolidays;

class Repository extends TestCase
{
    private $vietnamese_holidays;

    public function testIsWeekend()
    {
        $date = '2021-05-29';
        $this->assertTrue($this->vietnamese_holidays->is_weekend($date));
    }

    public function testIsNotWeekend()
    {
        $date = date('Y-m-d', strtotime('2021-05-27'));
        $this->assertFalse($this->vietnamese_holidays->is_weekend($date));
    }

    public function testIsHoliday()
    {
        $date = date('Y-m-d', strtotime('2021-10-10'));
        $this->assertTrue($this->vietnamese_holidays->is_holiday($date));
    }

    public function testIsNotHoliday()
    {
        $date = date('Y-m-d', strtotime('2021-01-02'));
        $this->assertFalse($this->vietnamese_holidays->is_holiday($date));
    }

    public function test5WorkingDays()
    {
        $start = date('Y-m-d', strtotime('2021-05-26'));
        $end = date('Y-m-d', strtotime('2021-05-31'));
        $number_working_days = $this->vietnamese_holidays->number_working_days($start, $end);
        $this->assertEquals(5, $number_working_days);
    }

    public function test5WorkingDaysWithHolidays()
    {
        $start = date('Y-m-d', strtotime('2021-05-26'));
        $end = date('Y-m-d', strtotime('2021-05-31'));
        $number_working_days = $this->vietnamese_holidays->number_working_days($start, $end, true);
        $this->assertEquals(3, $number_working_days);
    }

    public function testAddHolidays()
    {
        $holidays = [
            '2021-02-03' => [
                'name' => 'Ngày sinh của mẹ tôi',
                'priority' => 'highest'
            ],
            '2021-05-18' => [
                'name' => 'Ngày sinh của tôi',
                'priority' => 'highest'
            ]
        ];
        $this->assertTrue($this->vietnamese_holidays->add_holidays($holidays)->is_holiday('2021-02-03'));
    }

    public function testAddHolidaysFromFile()
    {
        $file = __DIR__ . '/2020.json';
        $result = $this->vietnamese_holidays->add_holidays_from_file($file)->is_holiday('2020-01-01');
        $this->assertTrue($result);
    }

    public function testGetInfoHoliday()
    {
        $file = __DIR__ . '/2020.json';
        $result = $this->vietnamese_holidays->add_holidays_from_file($file)->get_info_holiday('2020-01-01');
        $this->assertEquals('Tết Dương lịch', $result['name']);
    }

    public function testInstance()
    {
        $is_weekend = VietnameseHolidays::isWeekend('2021-05-29');
        $this->assertTrue($is_weekend);
        $is_holiday = VietnameseHolidays::isHoliday('2021-01-01');
        $this->assertTrue($is_holiday);
        $info_holiday = VietnameseHolidays::getInfoHoliday('2021-01-01');
        $this->assertEquals('Tết Dương lịch', $info_holiday['name']);
    }

    protected function setUp(): void
    {
        $this->vietnamese_holidays = new VietnameseHolidays();
    }
}
