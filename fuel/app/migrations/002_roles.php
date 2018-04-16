<?php 
namespace Fuel\Migrations;
class Roles
{
    function up()
    {
        \DBUtil::create_table('roles', array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'type' => array('type' => 'varchar', 'constraint' => 100),
        ), array('id'));
        \DB::query("INSERT INTO roles (id,type) VALUES ('1','admin');")->execute();
        \DB::query("INSERT INTO roles (id,type) VALUES ('2','profesores');")->execute();
        \DB::query("INSERT INTO roles (id,type) VALUES ('3','alumnos');")->execute();
    }
    function down()
    {
       \DBUtil::drop_table('roles');
    }
}