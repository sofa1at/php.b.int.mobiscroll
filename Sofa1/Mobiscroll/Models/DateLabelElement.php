<?php


namespace Sofa1\Mobiscroll\Models;


class DateLabelElement
{
    /**
     * @var \DateTime
     */
    public $From;

    /**
     * @var \DateTime
     */
    public $To;

    /**
     * @var string
     */
    public $InfoText;

    /**
     * DateLabelElement constructor.
     * @param $from
     * @param $to
     * @param $infoText
     */
    public function __construct($from, $to, $infoText)
    {
        $this->From = $from;
        $this->To = $to;
        $this->InfoText = $infoText;
    }

    public function __toString()
    {
        return sprintf("{start: new Date(%s,%s,%s), end: new Date(%s,%s,%s), text: '%s'}",
            $this->From->format("Y"),
            $this->From->format("m") - 1,
            $this->From->format("d"),
            $this->To->format("Y"),
            $this->To->format("m") - 1,
            $this->To->format("d"),
            $this->InfoText);
    }
}
