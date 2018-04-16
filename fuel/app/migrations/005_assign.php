<?php
namespace Fuel\Migrations;
class Assign
{
    function up()
    {
        \DBUtil::create_table('assign', array(
            'id_category' => array('type' => 'int', 'constraint' => 11,),
        ), array('id_category'), false, 'InnoDB', 'utf8_unicode_ci',
		    array(
		        array(
		            'constraint' => 'foreignKeyFromAssignToCategory',
		            'key' => 'id_category',
		            'reference' => array(
		                'table' => 'category',
		                'column' => 'id',
		            ),
		            'on_update' => 'CASCADE',
		            'on_delete' => 'RESTRICT'
		        ),
                
		    )
		);
    }
    function down()
    {
       \DBUtil::drop_table('assign');
    }
}