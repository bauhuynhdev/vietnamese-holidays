## Base usage

```php
use BauHuynh\VietnameseHolidays\Repository as VietnameseHolidays;

$vietnamese_holidays = new VietnameseHolidays();
// Check is holiday return bool
$vietnamese_holidays->is_holiday('2021-01-01');
// true
// Check is weekend return bool
$vietnamese_holidays->is_weekend('2021-01-01');
// true
// Get number workings day include holiday is bool return int
$vietnamese_holidays->number_working_days('2021-01-01', '2021-01-05', true);
// 2
// Add new list holidays
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
$vietnamese_holidays->add_holidays($holidays)->is_holiday('2021-02-03');
// true
// Add new list holidays from file
$file = '/path/2020.json';
$vietnamese_holidays->add_holidays_from_file($file)->is_holiday('2020-01-01');
// Get info holiday
$vietnamese_holidays->add_holidays_from_file($file)->get_info_holiday('2020-01-01');
// [
//    'name' => 'Tết Dương lịch',
//    'priority' => 'highest'
// ]
```
##Static methods
```php
use BauHuynh\VietnameseHolidays\Repository as VietnameseHolidays;

VietnameseHolidays::isHoliday('2021-01-01');
VietnameseHolidays::isWeekend('2021-01-01');
VietnameseHolidays::getInfoHoliday('2021-01-01');
```
