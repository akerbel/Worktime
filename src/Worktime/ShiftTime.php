<?php

namespace Worktime;

/**
 * A class for shift time.
 */
class ShiftTime extends \DateTime
{
    /**
     * Constructor. Rounds time up to \TIME_STEP minutes.
     *
     * @param string $time
     */
    public function __construct($time)
    {
        parent::__construct($time);

        // Rounding...
        $diff = $this->format('i') % \TIME_STEP;
        if ($diff) {
            $this->sub(\DateInterval::createFromDateString("{$diff} min"));
        }
    }

    /**
     * Turn the object to a string.
     *
     * return string
     */
    public function __toString()
    {
        return $this->format('H:i');
    }
}
