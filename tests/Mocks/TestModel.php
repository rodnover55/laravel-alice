<?php
namespace Rnr\Tests\Alice\Mocks;


use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $table = 'test';
    protected $guarded = [];
    public $timestamps = false;

    public function many() {
        return $this->hasMany(Test2Model::class, 'intfield');
    }

    public function one() {
        return $this->hasOne(Test2Model::class, 'intfield');
    }
}