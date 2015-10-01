<?php
class Grandopening_Validate_GoEndDate extends Zend_Validate_Abstract
{
    const MSG_ERROR_START = 'msgErrorStart';
    const MSG_ERROR_END = 'msgErrorEnd';

    protected $_messageVariables = array(
        'max_date' => '_max_date'
    );

    protected $_max_date;
    protected $_messageTemplates = array(
        self::MSG_ERROR_START => "Today is min countdown date",
        self::MSG_ERROR_END => "Max countdown date is 3 years : '%max_date%'"
    );

    public function isValid($value)
    {       
        $seconds_value = strtotime($value);
        $this->_setValue($this->_getDateString($seconds_value));
        if ($seconds_value < time()) {
            $this->_error(self::MSG_ERROR_START);
            return false;
        }
        $pluas3year = strtotime("+3 year");
        if ($seconds_value > $pluas3year) {
            $this->_max_date = $this->_getDateString($pluas3year);
            $this->_error(self::MSG_ERROR_END);
            return false;
        }
        return true;
    }
    
    protected function _getDateString($time) {
        $localeObject = Zend_Registry::get('Locale');
        $useMilitaryTime = ( stripos($localeObject->getTranslation(array("gregorian", "short"), 'time', $localeObject), 'a') === false );
        $date_format = ($useMilitaryTime) ? 'd-m-Y H:i' : 'd-m-Y h:i A' ;
        return date($date_format, $time) . ' (Timezone: ' . substr_replace(date('O'),":",3,0) . ')';
    }
    
}

?>
