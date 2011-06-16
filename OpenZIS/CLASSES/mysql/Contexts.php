<?php

class Contexts extends Zend_Db_Table
{
    protected $_schema = DBSCHEMA;
    protected $_name = 'context';

    protected $_primary = 'context_id';

    protected $_cols = array(
	'context_id'=>'context_id', 
	'context_desc'=>'context_desc'
    );
}