<?php
namespace Rnr\Tests\Alice;


use Rnr\Alice\FixtureExtractor;

class FixtureExtractorTest extends TestCase
{
    public function testExtract() {
        /** @var FixtureExtractor $extractor */
        $extractor = $this->app->make(FixtureExtractor::class);

//        $extractor->get
    }
}