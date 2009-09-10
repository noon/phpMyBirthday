<?php

class IndexController extends Zend_Controller_Action
{

    private $_monthMap = array('Januar' => 1, 'Februar' => 2, 'März' => 3, 'April' => 4, 'Mai' => 5, 'Juni' => 6, 'Juli' => 7,
                       'August' => 8, 'September' => 9, 'Oktober' => 10, 'November' => 11, 'Dezember' => 12);

    /**
     * Enter description here...
     *
     */
    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * Enter description here...
     *
     */
    public function indexAction()
    {
        $currentDate = new Zend_Date();
        $currentMonth = $currentDate->toString(Zend_Date::MONTH_SHORT);
        $currentDay = $currentDate->toString(Zend_Date::DAY_SHORT);
        $month = $this->_getParam('month', $currentMonth);

        if (false === is_numeric($month)) {
            $month = $this->_monthMap[urldecode($month)];
        }
        $currentDate->setMonth($month);
        $currentDate->setDay(1);
        $days = $currentDate->toString(Zend_Date::MONTH_DAYS);
        $this->view->selectedMonth = $month;
        $this->view->currentMonth  = $currentMonth;
        $this->view->currentDay    = $currentDay;

        $result = Bc_UserDTO::fetchAsArray(array('month' => $month));

        $month = array();
        for ($i = 1; $i <= $days; $i++) {
            $data = array();
            $data['date'] = $currentDate->toString(Zend_Date::DAY . '.' . Zend_Date::MONTH . '.' . Zend_Date::YEAR);

            $user = array();
            foreach ($result as $birthday) {
                if ($i == $birthday['birthday']) {
                    $user[] = $birthday;
                }
            }
            $data['user'] = empty($user) ? null : $user;

            $month[$i] = $data;
            $currentDate->add('24:00:00', Zend_Date::TIMES);
        }

        $this->view->month = $month;
    }

    /**
     * ... simple action to return some json data
     *
     */
    public function gridAction()
    {
        $users = Bc_UserDTO::fetchAsArray(null, 'firstname');

        $json = '{ "identifier": "id", "idAttribute":"id", "label": "firstname","items": ';
        $json .= Zend_Json::encode($users);
        $json .= '}';

        echo $json;
        exit();
    }
}

