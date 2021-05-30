<?php

namespace BauHuynh\VietnameseHolidays;

use BauHuynh\VietnameseHolidays\Exception as VietnameseHolidayException;
use DateTime;
use Exception;

/**
 * Class Repository
 * @method static static isWeekend(string $date)
 * @method static static isHoliday(string $date)
 * @method static static getInfoHoliday(string $date)
 * @method
 * @package BauHuynh\VietnameseHolidays
 */
class Repository
{
    private static $instance;
    private $default_date_format = 'Y-m-d';
    private $holiday_path = __DIR__ . '/Holidays';
    private $holidays = [];

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($name, $arguments)
    {
        $instance = static::getInstance();
        $name = $instance->from_camel_case($name);
        if (!method_exists(Repository::class, $name)) {
            throw new VietnameseHolidayException('Method not exist');
        }
        return $instance->{$name}(...$arguments);
    }

    /**
     * @return static
     */
    private static function getInstance()
    {
        if (!static::$instance instanceof self) {
            static::$instance = new self;
        }

        return static::$instance;
    }

    /**
     * @param $input
     * @return string
     */
    function from_camel_case($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * @param string $start
     * @param string $end
     * @param bool $is_holiday
     * @return int
     * @throws Exception
     */
    public function number_working_days($start, $end, $is_holiday = false)
    {
        $start = $this->is_valid_date($start);
        $end = $this->is_valid_date($end);
        if (!$start || !$end) {
            return 0;
        }
        if ($start > $end) {
            return 0;
        }
        $interval = $this->diff($start, $end);
        if ($is_holiday) {
            while ($start < $end) {
                if ($this->is_holiday($start) || $this->is_weekend($start)) {
                    $interval--;
                }
                $start = date($this->default_date_format, strtotime("{$start} +1 day"));
            }
        }
        return $interval;
    }

    /**
     * @param string $date
     * @return string|false
     */
    private function is_valid_date($date)
    {
        $dt = DateTime::createFromFormat($this->default_date_format, $date);
        if ($dt && $dt->format($this->default_date_format) === $date) {
            return $date;
        }

        return false;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return int
     * @throws Exception
     */
    private function diff($start_date, $end_date)
    {
        $start_datetime = new DateTime($start_date);
        $end_datetime = new DateTime($end_date);
        return $end_datetime->diff($start_datetime)->days;
    }

    /**
     * @param string $date
     * @return bool
     */
    public function is_holiday($date)
    {
        if (!($date = $this->is_valid_date($date))) {
            return false;
        }
        $holidays = $this->get_holidays($date);
        return isset($holidays[$date]);
    }

    private function get_holidays($date)
    {
        $parts_date = $this->get_part_date($date);
        $holidays = $this->get_holidays_from_year($parts_date['year']);
        if (!empty($this->holidays)) {
            $holidays = array_merge($holidays, $this->holidays);
        }
        return $holidays;
    }

    /**
     * @param $date
     * @return array
     */
    private function get_part_date($date)
    {
        if (!($date = $this->is_valid_date($date))) {
            return [];
        }
        $parts = ['day' => 'd', 'month' => 'm', 'year' => 'Y'];
        $data = [];
        foreach ($parts as $key => $part) {
            $data[$key] = date($part, strtotime($date));
        }
        return $data;
    }

    /**
     * @param $year
     * @return array
     */
    public function get_holidays_from_year($year)
    {
        $file = "{$this->holiday_path}/{$year}.json";
        if (!file_exists($file)) {
            return [];
        }
        $file = file_get_contents($file);
        $json = json_decode($file, true);
        if (empty($json)) {
            return [];
        }
        return $json;
    }

    /**
     * @param string $date
     * @return bool
     */
    public function is_weekend($date)
    {
        if (!($date = $this->is_valid_date($date))) {
            return false;
        }
        return (date('N', strtotime($date)) >= 6);
    }

    /**
     * @param array $holidays
     * @return $this
     */
    public function add_holidays($holidays)
    {
        $this->holidays = array_merge($this->holidays, $holidays);
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function add_holidays_from_file($path)
    {
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if (!empty($content)) {
                $holidays = json_decode($content, true);
                $this->holidays = array_merge($this->holidays, $holidays);
            }
        }

        return $this;
    }

    /**
     * @param string $date
     * @return array
     */
    public function get_info_holiday($date)
    {
        if (!($date = $this->is_valid_date($date))) {
            return [];
        }
        $holidays = $this->get_holidays($date);
        return isset($holidays[$date]) ? $holidays[$date] : [];
    }
}
