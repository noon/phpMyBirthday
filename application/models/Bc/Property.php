<?php
class Bc_Property extends Zend_Db_Table_Abstract
{
    /**
     * The name of the table
     *
     * @var string
     */
    protected $_name = 'property';

    /**
     * The primary key of the table
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Update the property of a user or insert it if it does not exist
     *
     * @param integer $userId the id of the user
     * @param string $key the name (key) of the property
     * @param string $value the value to be set
     *
     * @return void
     */
    public function ins($userId, $key, $value)
    {
        $table = new self();

        $where = '`key` = ' . $this->_db->quote($key) . ' AND user_id = ' . $this->_db->quote($userId);
        $select = $this->_db->select('id')
                            ->from('property')
                            ->where($where);
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchColumn();
        $data = array('value' => $value);
        if (false === empty($result)) {
            $table->update($data, $where);
        } else {
            $data['user_id'] = $userId;
            $data['key'] = $key;
            $table->insert($data);
        }
    }

    /**
     * Get all Properties for a given user
     *
     * @param integer $userId the id of the user
     *
     * @return array the properties
     */
    public function getAll($userId)
    {
        $select = $this->_db->select()->from('property')->where('user_id = ' . $this->_db->quote($userId));
        $stmt = $this->_db->query($select);
        return $stmt->fetchAll();
    }
}
?>