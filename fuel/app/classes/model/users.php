<?php 
class Model_Users extends Orm\Model
{
    protected static $_table_name = 'users';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id', // both validation & typing observers will ignore the PK
        'username' => array(
            'data_type' => 'varchar'   
        ),
        'password' => array(
            'data_type' => 'varchar'   
        ),
        'description' => array(
            'data_type' => 'varchar'   
        ),
        'email' => array(
            'data_type' => 'varchar'   
        ),
        'id_rol',
        
    );
protected static $_belongs_to = array(
        'roles' => array(
            'key_from' => 'id_rol',
            'model_to' => 'Model_Roles',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        )
    );
protected static $_many_many = array(
    'category' => array(
            'key_from' => 'id',
            'key_through_from' => 'id_user',
            'table_through' => 'belong',
            'key_through_to' => 'id_category',
            'model_to' => 'Model_Category',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
}