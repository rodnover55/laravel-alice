<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Builder;

class AddLinksTable extends Migration
{
    /** @var Builder  */
    private $schema;

    public function __construct()
    {
        $this->schema = Schema::connection($this->connection);
    }

    public function up() {
        $this->schema->create('links', function (Blueprint $table) {
            $table->integer('test_id');
            $table->integer('test2_id');
        });
    }

    public function down() {
        $this->schema->drop('links');
    }
}