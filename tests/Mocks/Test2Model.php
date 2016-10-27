<?php
namespace Rnr\Tests\Alice\Mocks;


use Illuminate\Database\Eloquent\Model;

class Test2Model extends Model
{
    protected $table = 'test2';
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id2';

    public function manyToMany() {
        return $this->belongsToMany(TestModel::class, 'links', 'test2_id', 'test_id');
    }
}