<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Builder;

class CreateTestStructure extends Migration
{
    /** @var Builder  */
    private $schema;

    public function __construct()
    {
        $this->schema = Schema::connection($this->connection);
    }

    public function up() {
        $this->schema->create('test', function (Blueprint $table) {
            $table->increments('id');
            $table->string('field1');
        });

        $this->schema->create('test2', function (Blueprint $table) {
            $table->increments('id2');
            $table->integer('intfield');
        });
    }

    public function down() {
        $this->schema->drop('test');
        $this->schema->drop('test2');
    }
}