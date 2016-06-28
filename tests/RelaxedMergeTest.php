<?php

namespace RelaxedMerge\Test;

use PhpMerge\Test\AbstractPhpMergeTest;
use RelaxedMerge\RelaxedMerge;

/**
 * @group php-merge
 */
class RelaxedMergeTest extends AbstractPhpMergeTest
{
    public function setUp()
    {
        parent::setUp(new RelaxedMerge());
    }

    public function testNewLines()
    {
        // Set break point to inspect failures.
        parent::testNewLines();
    }

    public function testSingleChange()
    {
        // Set break point to inspect failures.
        parent::testSingleChange();
    }
}