# LaravelAlice

A [Laravel](https://laravel.com/) package to manage fixtures with [nelmio/alice](https://github.com/nelmio/alice).

## Installation

This is installable via [Composer](https://getcomposer.org/) as [rnr/laravel-alice](https://packagist.org/packages/rnr/laravel-alice)
 
    composer install --dev rnr/laravel-alice
    
## Basic usage

Create `yml` fixture file as it is described [nelmio/alice](https://github.com/nelmio/alice)

```yaml
Nelmio\Entity\User:
    user{1..10}:
        username: '<username()>'
        fullname: '<firstName()> <lastName()>'
        birthDate: '<date()>'
        email: '<email()>'
        favoriteNumber: '50%? <numberBetween(1, 200)>'

Nelmio\Entity\Group:
    group1:
        name: Admins
        owner: '@user1'
        members: '<numberBetween(1, 10)>x @user*'
        created: '<dateTimeBetween("-200 days", "now")>'
        updated: '<dateTimeBetween($created, "now")>'
```
Class `FixturelLoader` has one significant method [load](https://github.com/rodnover55/laravel-alice/blob/master/src/FixturesLoader.php#L39-L39) to load models.
That method receive one or array of files with data to load. You can load this fixture to database with next code:

```PHP
<?php
namespace Rnr\Tests\Alice;

use Orchestra\Testbench\TestCase as ParentTestCase;
use Rnr\Alice\FixturesLoader;
use Nelmio\Entity\User;
use Nelmio\Entity\Group;

class TestCase extends ParentTestCase
{
    /** @var FixturesLoader */
    protected $fixturesLoader;

    protected function setUp()
    {
        parent::setUp();

        $this->fixturesLoader = $this->app->make(FixturesLoader::class);
    }
    
    public function testLoadingFixtures() {
        $objects = $this->fixturesLoader->load('fixture.yml');
        
        $users = User::all();
        
        $this->assertEquals(array_map($objects, function ($model) {
            return $model->getKey();
        }), $users->modelKeys());
    }
}
```

It loads data for next models:

```PHP
<?php
namespace Nelmio\Entity;

use Illuminate\Database\Eloquent\Model;

class User extends Model  {
    protected $table = 'users';
}

class Group extends Model {
    protected $table = 'groups';
    
    public function owner() {
        return $this->belnogsTo(User::class);
    }
}
```

## Restrictions

You can use id to specify related models in relationships, but these models should be already create in database.

## Extracting fixtures from database

If you add [GenerateFixtureCommand](https://github.com/rodnover55/laravel-alice/blob/master/src/Commands/DB/GenerateFixtureCommand.php) 
to your console kernel you can export data to yml from existing database. 
This class add new command `db:generate-fixture` to artisan. This command extract fixtures from database. 
Command takes array of models with relations in specific format:

```
php artisan db:generate-fixture Model(relations:relation1,realation2.subrelation)=1,2,3-5,17,20-25 Model2(relations:hasOne)=*
```


