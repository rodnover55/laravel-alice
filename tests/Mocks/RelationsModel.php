<?php
namespace Rnr\Tests\Alice\Mocks;


use Illuminate\Database\Eloquent\Model;

class RelationsModel extends Model
{
    protected $table = 'relations';
    protected $guarded = [];

    public function belongs() {
        return $this->belongsTo(TestModel::class);
    }
}