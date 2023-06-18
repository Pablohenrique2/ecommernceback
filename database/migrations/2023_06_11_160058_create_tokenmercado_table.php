<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTokenmercadoTable extends Migration
{
    public function up()
    {
        Schema::create('tokenmercado', function (Blueprint $table) {
            $table->increments('id');
            $table->string('access_token');
            $table->string('code');
            $table->string('client_id');
            $table->string('client_secret');
            $table->string('idloja');
            $table->string('first_name');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tokenmercado');
    }
}

