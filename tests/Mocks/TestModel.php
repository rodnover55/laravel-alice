<?php
namespace Rnr\Tests\Alice\Mocks;


use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $table = 'test';
    protected $guarded = [];
    public $timestamps = false;
}