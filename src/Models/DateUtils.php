<?php

namespace DUT\Models;


class DateUtils
{
    /**
     * Format Current Date For DB Insertion.
     *
     * @return string
     */
    public static function getFormattedCurrentDate() {
        $date = getdate();
        $formattedDate = $date["year"]
            . "-" . str_pad($date["mon"], 2, '0', STR_PAD_LEFT)
            . "-" . str_pad($date["mday"], 2, '0', STR_PAD_LEFT);

        return $formattedDate;
    }
}