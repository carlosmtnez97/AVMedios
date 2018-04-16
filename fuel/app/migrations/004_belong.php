<?php
namespace Fuel\Migrations;
class Belong
{
    function up()
    {
        \DBUtil::create_table('belong', array(
            'id_user' => array('type' => 'int', 'constraint' => 11),
            'id_category' => array('type' => 'int', 'constraint' => 11),
        ), array('id_user','id_category'), false, 'InnoDB', 'utf8_unicode_ci',
		    array(
		        array(
		            'constraint' => 'foreignKeyFromBelongToUser',
		            'key' => 'id_user',
		            'reference' => array(
		                'table' => 'users',
		                'column' => 'id',
		            ),
		            'on_update' => 'CASCADE',
		            'on_delete' => 'CASCADE'
		        ),
                array(
                    'constraint' => 'foreignKeyFromBelongToCategory',
                    'key' => 'id_category',
                    'reference' => array(
                        'table' => 'category',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'RESTRICT'
                )
		    )
		);
    }
    function down()
    {
       \DBUtil::drop_table('belong');
    }
}