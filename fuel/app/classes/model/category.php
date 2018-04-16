<?php 
class Model_Category extends Orm\Model
{
    protected static $_table_name = 'category';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id',
        'title' => array(
            'data_type' => 'text'   
        ),
       
    );
    protected static $_many_many = array(
    'users' => array(
            'key_from' => 'id',
            'key_through_from' => 'id_category',
            'table_through' => 'belong',
            'key_through_to' => 'id_user',
            'model_to' => 'Model_Users',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
 }