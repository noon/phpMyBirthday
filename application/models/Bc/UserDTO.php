<?php

class Bc_UserDTO
{
    /**
     * Property map e.g. (id => val, firstname => val)
     *
     * @var array
     */
    private $_properties;

    /**
     * The constructor for a UserDTO object
     *
     * @return void
     */
    public function __construct()
    {
        $this->_table = new Bc_User();
        $this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
    }

    /**
     * Set the value for a given key in the property map
     *
     * @param string $key the key
     * @param string $value the value to be set
     *
     * @return void
     */
    public function set($key, $value)
    {
        $this->_properties[$key] = $value;
    }

    /**
     * Get a value of a given key in the property map
     *
     * @param string $key the key
     *
     * @return string the value of the key in the property map
     */
    public function get($key)
    {
        if (false === empty($this->_properties[$key])) {
            return $this->_properties[$key];
        }
        return null;
    }

    /**
     * Save the data and the properties of the user
     *
     * @return integer the id of the user
     */
    public function save()
    {
        $data  = array('firstname'  => $this->_properties['firstname'],
                       'secondname' => $this->_properties['secondname'],
                       'birthdate'  => $this->_properties['birthdate']);
        if (false === empty($this->_properties['id'])) {
            $this->_table->update($data, 'id = ' . $this->_db->quote($this->_properties['id']));
        } else {
            $this->_properties['id'] = $this->_table->insert($data);
        }

        $properties = $this->_properties;
        unset($properties['id'], $properties['firstname'], $properties['secondname'], $properties['birthdate']);

        if (false === empty($properties)) {
            $propertyModel = new Bc_Property();
            foreach ($properties as $key => $value) {
                $propertyModel->ins($this->_properties['id'], $key, $value);
            }
        }

        return $this->_properties['id'];
    }

    /**
     * Get datasets matching given criteria
     *
     * @param array $data key value pairs to search for (e.g. array('id' => 28) - finds the user with id 28)
     *
     * @return array multidimensional array with userdata matching the given criteria
     */
    public static function fetch($data = null, $order = null)
    {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $db->select()->from('user');
        if (false === empty($data) && true === array_key_exists('month', $data)) {
            $select->where('MONTH(birthdate) = ' . $db->quote($data['month']));
            unset($data['month']);
        }
        if (false === empty($data)) {
            foreach ($data as $key => $value) {
                $select->where($key . ' = ' . $value);
            }
        }
        if (false === empty($order)) {
            $select->order($order);
        }
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        $return = array();
        if (false === empty($result)) {
            foreach ($result as $data) {
                $return[] = self::load($data);
            }
        }
        return $return;
    }

    public static function fetchAsArray($data = null, $order = null)
    {
        $user = self::fetch($data, $order);
        $result = array();
        if (false === empty($user)) {
            foreach ($user as $data) {
                $birthday = date_parse($data->get('birthdate'));
                $modifier = 0;

                $curMon = (int)date('m');
                $bMon = $birthday['month'];

                if ($curMon < $bMon) {
                    $modifier = 'wird';
                } else if ($curMon > $bMon) {
                    $modifier = 'wurde';
                } else {
                    $modifier = 'ist';
                }

                $result[] = array('id'         => $data->get('id'),
                                  'firstname'  => $data->get('firstname'),
                                  'secondname' => $data->get('secondname'),
                                  'birthdate'  => $birthday['day'] . '.' . $birthday['month'] . '.' . $birthday['year'],
                                  'birthday'   => $birthday['day'],
                                  'birthmonth' => $birthday['month'],
                                  'birthyear'  => $birthday['year'],
                                  'age'        => $modifier . ' ' . (date('Y') - $birthday['year']));
            }
        }
        return $result;
    }

    /**
     * Get an UserDTO object prefilled with values
     *
     * @param array $data key value pairs to be set
     *
     * @return UserDTO the object
     */
    public static function load($data)
    {
        $obj = new self();
        foreach ($data as $key => $value) {
            $obj->set($key, $value);
        }

        $prop = new Bc_Property();
        $prop = $prop->getAll($data['id']);
        if (false === empty($prop)) {
            foreach($prop as $data) {
                $obj->set($data['key'], $data['value']);
            }
        }

        return $obj;
    }

    /**
     * Remove a user by id
     *
     * @param integer $userId the id of the user to be removed
     *
     * @return bool true if deleted successfully else false
     */
    public static function remove($userId)
    {
        $userId = Zend_Db_Table_Abstract::getDefaultAdapter()->quote($userId);
        $property = new Bc_Property();
        $property->delete('user_id = ' . $userId);
        $user = new Bc_User();
        $result = $user->delete('id = ' . $userId);
        if ($result < 1) {
        	return false;
        } else {
        	return true;
        }
    }

    /**
     * Get users with birthday in x days
     *
     * @param integer $days day count (1 = tomorrow, 0 = today)
     *
     * @return array array of UserDTO objects
     */
    public static function getUsersWithBirthdayInXDays($days)
    {
        $date = new Zend_Date();
        $date->add($days * 24 . ':00:00', Zend_Date::TIMES);
        $filter = array('MONTH(birthdate)' => $date->toString('MM'),
                        'DAY(birthdate)'   => $date->toString('dd'));
        return self::fetch($filter);
    }
}
?>