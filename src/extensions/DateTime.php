<?php


namespace rezident\attachfile\extensions;


class DateTime extends \DateTime
{
    public function getMysqlDateTimeString()
    {
        return $this->format('Y-m-d H:i:s');
    }
}