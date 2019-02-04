<?php
/**
 * User: zerozelta
 * Date: 27/07/2018
 * Time: 09:10 PM
 */

use Illuminate\Database\Schema\Blueprint;

$DFWStructs = array(
    "dfw_users" => function (Blueprint $table) {
        $table->increments('id');
        $table->string('nick',100)->unique();
        $table->string('email',100)->unique()->nullable();
        $table->string('encodedKey',100);

        $table->timestamps();
    },

    "dfw_sessions" => function (Blueprint $table) {
        $table->increments('id');

        $table->string('token',100);
        $table->string('agent',300);
        $table->string('ip',30);
        $table->timestamp('expire');
        $table->integer('idUser')->nullable();
        $table->string('site',500);

        $table->timestamps();
    },

    "dfw_access" => function (Blueprint $table) {
        $table->increments('id');
        $table->string('name',60)->unique();
        $table->string('description',140);
        $table->timestamps();
    },

    "dfw_credentials" => function (Blueprint $table) {
        $table->increments('id');
        $table->string('name',60)->unique();
        $table->string('description',140);
        $table->timestamps();
    },

    "dfw_access_credentials" => function (Blueprint $table) {
        $table->integer('idAccess');
        $table->integer('idCredential');

        $table->primary(["idCredential","idAccess"]);
    },

    "dfw_users_credentials" => function (Blueprint $table) {
        $table->integer('idUser');
        $table->integer('idCredential');

        $table->primary(["idCredential","idUser"]);
    },



);
