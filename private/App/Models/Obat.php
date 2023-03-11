<?php

class Obat extends Model
{

  public function insert($post, $fetch = null)
  {

    $this->_db->table('obat');
    return $this->_db->insert($post, $fetch);
  }

  public function read($attr, $where = null, $fetch = 'ARRAY')
  {

    $this->_db->table('obat');
    return $this->_db->select($attr, $where, $fetch);
  }

  public function update($data, $where, $fetch = null)
  {

    $this->_db->table('obat');
    return $this->_db->update($data, $where, $fetch);
  }

  public function delete($where, $fetch = null)
  {

    $this->_db->table('obat');
    return $this->_db->delete($where, $fetch);
  }
}
