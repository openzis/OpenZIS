<?php

class Contexts extends Zend_Db_Table
{

#    protected $_schema = 'OPENZIS';
	protected $_name = 'CONTEXT';

    protected $_primary = 'context_id';

    protected $_cols = array(
	'context_id'=>'context_id', 
	'context_desc'=>'context_desc'
    );
}