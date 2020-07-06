<?php


namespace Sofa1\Mobiscroll\Models;


class AbstractStationBusinessHolidayDto
{
    /**
     * @var int
     */
    public $Id;
    /**
     * @var int
     */
    public $StationId;
    /**
     * @var \DateTime
     */
    public $From;
    /**
     * @var \DateTime
     */
    public $To;
    /**
     * @var string|null
     */
    public $InfoText;
    /**
     * @var string|null
     */
    public $CreatedBy;
    /**
     * @var \DateTime|null
     */
    public $CreatedOn;
    /**
     * @var string|null
     */
    public $ModifiedBy;
    /**
     * @var \DateTime|null
     */
    public $ModifiedOn;
}
