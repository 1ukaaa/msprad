<?php
abstract class UtilsFunctions
{
    static function differenceBetweenTwoDates(string $date1, string $date2)
    {
        $debut = new DateTime($date1);
        $fin = new DateTime($date2);
        $interval = $debut->diff($fin);
        return $interval->s;
    }
}
