<?php
namespace Worktime;
/**
 * A class for shifts
 */
class Shift
{
    /**
     * @var ShiftTime The start of shifts time.
     */
    private $start;
    
    /**
     * @var ShiftTime The end of shifts time.
     */
    private $end;
    
    /**
     * @var array An array of worktime.
     */
    private $workshift;
    
    /**
     * @var array An array of daytime.
     */
    private $dayshift;
    
    /**
     * Set new start time.
     *
     * @param ShiftTime $time
     */
    public function setStart(ShiftTime $time)
    {
        $this->start = $time;
    }
    
    /**
     * Get start time.
     *
     * @return ShiftTime
     */
    public function getStart()
    {
        return $this->start;
    }
    
    /**
     * Set new end time.
     *
     * @param ShiftTime $time
     */
    public function setEnd(ShiftTime $time)
    {
        $this->end = $time;
    }
    
    /**
     * Get end time.
     *
     * @return ShiftTime
     */
    public function getEnd()
    {
        return $this->end;
    }
    
    /**
     * Constructor.
     *
     * @param ShiftTime $start The start of shifts time.
     * @param ShiftTime $end The end of shifts time.
     *
     * return Shift
     */
    public function __construct(ShiftTime $start, ShiftTime $end) 
    {
        $this->start = $start;
        $this->end = $end;
        
        // Create timestap as \DateInterval for using in \DatePeriod.
        $timeStep = self::minutesToInterval(\TIME_STEP);
        
        // Create worktime as array
        // If worktime doesn`t pass a midnight
        if (!$this->start->diff($this->end)->invert) {
            foreach (new \DatePeriod($this->start, $timeStep, $this->end) as $date){
                $this->workshift[] = $date->format('H:i');
            }
            
        // If worktime does pass a midnight, then we have 2 periods insted 1 - before midnight and after midnight
        } else {
            foreach (new \DatePeriod(new ShiftTime('00:00'), $timeStep, $this->end) as $date){
                $this->workshift[] = $date->format('H:i');
            }
            foreach (new \DatePeriod($this->start, $timeStep, new ShiftTime('24:00')) as $date){
                $this->workshift[] = $date->format('H:i');
            }
        }
        
        $nighttimeStart = new ShiftTime(\NIGHTTIME_START);
        $nighttimeEnd = new ShiftTime(\NIGHTTIME_END);
        
        // Create dayshift as array
        foreach (new \DatePeriod($nighttimeEnd, $timeStep, $nighttimeStart) as $date){
            $this->dayshift[] = $date->format('H:i');
        }
    }

    /**
     * Get length of the shift.
     *
     * @return \DateInterval
     */
    public function getLength()
    {
        return self::minutesToInterval(count($this->workshift) * \TIME_STEP);
    }
    
    /**
     * Get nighttime work.
     *
     * @return \DateInterval
     */
    public function getNighttime()
    {
        return self::minutesToInterval(count(array_diff($this->workshift, $this->dayshift)) * \TIME_STEP);
    }
    
    /**
     * Get daytime work.
     *
     * @return \DateInterval
     */
    public function getDaytime()
    {
        return self::minutesToInterval(count(array_intersect($this->workshift, $this->dayshift)) * \TIME_STEP); 
    }
    
    /**
     * Turn \DateInterval to minutes.
     *
     * @param \DateInterval $interval 
     *
     * @return int
     */
    public static function intervalToMinutes(\DateInterval $interval)
    {
        return ($interval->format('%H')*60) + $interval->format('%i');
    }
    
    /**
     * Turn minutes to \DateInterval.
     *
     * @param int $minutes 
     *
     * @return \DateInterval
     */
    public static function minutesToInterval(int $minutes)
    {
        return \DateInterval::createFromDateString(floor(($minutes / 60)). ' hour '.($minutes % 60) . ' min');
    }
    
}
