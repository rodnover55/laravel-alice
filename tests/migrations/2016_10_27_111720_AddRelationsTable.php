<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Builder;

class AddRelationsTable extends Migration
{
    /** @var Builder  */
    private $schema;

    public function __construct()
    {
        $this->schema = Schema::connection($this->connection);
    }

    public function up() {
        $this->schema->create('relations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('belongs_id')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        $this->schema->drop('relations');
    }
}