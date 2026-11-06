<?php

use Chaima24\AlgeriaGeo\Tests\CoreOnlyTestCase;
use Chaima24\AlgeriaGeo\Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature');
pest()->extend(CoreOnlyTestCase::class)->in('CoreOnly');
