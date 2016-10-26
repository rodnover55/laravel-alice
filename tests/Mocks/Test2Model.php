<?php
namespace Rnr\Tests\Alice\Mocks;


use Illuminate\Database\Eloquent\Model;

class Test2Model extends Model
{
    protected $table = 'test2';
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id2';
}