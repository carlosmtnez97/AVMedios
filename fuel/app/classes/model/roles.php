<?php 
class Model_Roles extends Orm\Model
{
    protected static $_table_name = 'roles';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id',
        'type' => array(
            'data_type' => 'text'   
        ),
    );
    protected static $_has_many = array(
        'users' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Users',
            'key_to' => 'id_rol',
            'cascade_save' => false,
            'cascade_delete' => false,
        )
    );
}