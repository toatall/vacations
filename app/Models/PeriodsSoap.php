<?php

namespace App\Models;

use DateInterval;

/**
 * @method PeriodsSoap getPeriods($year)
 */
class PeriodsSoap extends SoapModel
{    

    /**
     * @var array
     */
    protected $typesVacation = [];

    /** 
     * @var array 
     */
    protected $colors;

    /**
     * {@inheritdoc}
     */
    public function __construct($soapUrl, $soapOptions = [], $cacheName=null)
    {
        $soapUrl = $soapUrl ?? env('SOAP.URL.PERIODS');
        $this->colors = ['text-purple-400', 'text-blue-400', 'text-pink-400', 'text-yellow-400', 'text-gray-400', 'text-red-400'];
        parent::__construct($soapUrl, $soapOptions, $cacheName);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = parent::getDataAsArray();
        return $this->convert($data['item'] ?? $data);
    }

    public function dateFormat()
    {
        return 'd.m.Y';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataAsArray()
    {
        $data = parent::getDataAsArray();
        return $this->convert($data['item'] ?? $data);
    }

    /**
     * Подготовка данных
     * @param array $data
     * @return array
     */
    private function convert($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        $res = [];
        foreach($data as $item) {
            $dep = explode('\\', $item['Department'])[1] ?? $item['Department'];
            $fio = $item['FullName'];
            $dateStart = new \DateTimeImmutable($item['DateStart']);
            $dateEnd = new \DateTimeImmutable($item['DateEnd']);           
            $res[$dep][$fio][] = [
                'dateStart' => $dateStart,
                'dateEnd' => $dateEnd,                
                'type' => $item['TypeVacation'],
                'count' => $dateEnd->diff($dateStart)->d,   
                'status' => $item['Status'],
            ];
            if (!isset($this->typesVacation[$item['TypeVacation']])) {               
                $this->setTypeVacation($item['TypeVacation']);
            }
        }
        ksort($res);
        return $res; 
    }

    /**
     * @param string $year
     * @return array
     */
    public static function generateCalendar($year)
    {
        $data = [];
        $dateStart = new \DateTimeImmutable(date('01.01.' . $year));
        $dateEnd = new \DateTimeImmutable(date('31.12.' . $year));
        $curDate = $dateStart;
        while($curDate <= $dateEnd) {

            if (!isset($data[$curDate->format('m')])) {
                $data[$curDate->format('m')] = [
                    'name' => $curDate->format('F'),
                    'days' => $curDate->format('t'),
                    'daysData' => [],
                ];
            }
            
            $data[$curDate->format('m')]['daysData'][] = [
                'date' => $curDate,
                'day' => $curDate->format('d'),
            ];

            $curDate = $curDate->add(new \DateInterval('P1D'));
        }   
        return $data; 
    }

    /**
     * @param string $type
     */
    protected function setTypeVacation($type) 
    {     
        $color = array_shift($this->colors);
        $this->typesVacation[$type] = $color;
    }

    /**
     * @return array
     */
    public function getTypesVacation()
    {
        return $this->typesVacation;
    }

    public function getStatistics($year)
    {
        // всего сотрудников       
        $employees = [];

        // сотрудники в отпуске
        $employeesOnVacation = [];

        // сотрудники, которые пойдут в отпуск в течении недели
        $employeesSoonVacation = [];

        // количество отпусков по дням
        $countVacations = [];
       
        $data = $this->getDataAsArray();
        $data = $data['item'] ?? $data;

        $dtNow = new \DateTimeImmutable(date('d.m.Y'));
        $dtWeekStart = $dtNow->add(new \DateInterval('P1D'));
        $dtWeekEnd = $dtNow->add(new \DateInterval('P8D'));

        // по отделам
        foreach($data as $depName => $dep) {           
            
            // по сотрудникам
            foreach($dep as $fio => $empl) {
                                
                $employees[$fio] = true;                
                $periods = [];

                // сортировка по дате начала отпуска
                $this->arraySortByValue($empl, 'dateStart');
                
                // соединение периодов (если они идут друг за другом)
                foreach($empl as $period) {                                   

                    $dateStart = $period['dateStart'];
                    $dateEnd = $period['dateEnd'];
                    while ($dateStart <= $dateEnd) {
                        $dStr = $dateStart->format('d.m.Y');
                        
                        if (isset($countVacations[$dStr])) {
                            $countVacations[$dStr]++;
                        }
                        else {
                            $countVacations[$dStr] = 1;
                        }                    
                        $dateStart = $dateStart->add(new DateInterval('P1D'));
                    }

                    if (!$periods) {
                        $periods[] = [
                            'dateStart' => $period['dateStart'],
                            'dateEnd' => $period['dateEnd'],
                            'status' => $period['status'],
                        ];                        
                    } 
                    else {
                        $last = &$periods[count($periods)-1];
                        if ($last['dateEnd']->add(new \DateInterval('P1D'))->format('dmY') == $period['dateStart']->format('dmY')) {
                            $last['dateEnd'] = $period['dateEnd'];
                        }
                        else {
                            $periods[] = [
                                'dateStart' => $period['dateStart'],
                                'dateEnd' => $period['dateEnd'],
                                'status' => $period['status'],
                            ]; 
                        }
                    }
                }

                // перебор периодов
                foreach($periods as $period) {

                    // сотрудники в текущий момент в отпуске
                    if ($dtNow >= $period['dateStart'] && $dtNow <= $period['dateEnd'] && $period['status'] == 'X') {
                        $newItem = $period;
                        $newItem['fio'] = $fio;
                        $newItem['department'] = $depName;
                        $newItem['dateStartText'] = $period['dateStart']->format('d.m.Y');
                        $newItem['dateEndText'] = $period['dateEnd']->format('d.m.Y');
                        $newItem['countDays'] = (int) (($period['dateEnd']->diff($period['dateStart'])->d) + 1);
                        $newItem['countDaysNow'] = (int) (($dtNow->diff($period['dateStart'])->d) + 1);
                        $employeesOnVacation[] = $newItem;                    
                    }

                    // сотрудники, которые пойдут в отпуск в ближайшую неделю
                    if ($period['dateStart'] >= $dtWeekStart && $period['dateStart'] <= $dtWeekEnd && $period['status'] == 'X') {
                        $newItem = $period;
                        $newItem['fio'] = $fio;
                        $newItem['department'] = $depName;
                        $newItem['dateStartText'] = $period['dateStart']->format('d.m.Y');
                        $newItem['dateEndText'] = $period['dateEnd']->format('d.m.Y');
                        $newItem['countDays'] = (int) (($period['dateEnd']->diff($period['dateStart'])->d) + 1);
                        $newItem['countDaysNow'] = (int) (($dtNow->diff($period['dateStart'])->d) + 1);
                        $employeesSoonVacation[] = $newItem;                    
                    }                    
                }
            }
        }

        $this->arraySortByValue($employeesOnVacation, 'fio');

        $countVacationsByDate = [];

        $dateStart = new \DateTimeImmutable("01.01.$year");
        $dateEnd = new \DateTimeImmutable("31.12.$year");
        while ($dateStart <= $dateEnd) {
            $dStr = $dateStart->format('d.m.Y');
            $countVacationsByDate[$dStr] = $countVacations[$dStr] ?? 0;
            $dateStart = $dateStart->add(new DateInterval('P1D'));
        }

        $countVacationsByDateLabels = array_map(function($val) {
            if (preg_match('/(\d{2}).(\d{2}).(\d{4})/', $val, $matches)) {
                if (count($matches) >= 3) {
                    return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
                }
            }
            return $val;        
        }, array_keys($countVacationsByDate));
                
        
        return [
            'emplCount' => count($employees),
            'emplOnVacation' => $employeesOnVacation,       
            'employeesSoonVacation' => $employeesSoonVacation,     
            // 'countVacationsByDate' => json_encode($countVacationsByDate),
            'countVacationsByDateDatas' => json_encode(array_values($countVacationsByDate)),
            'countVacationsByDateLabels' => json_encode($countVacationsByDateLabels), //json_encode(array_keys($countVacationsByDate)),
            'year' => $year,
        ];

    }

}