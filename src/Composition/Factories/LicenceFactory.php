<?php

namespace Me\OopTest\Composition\Factories;

use Me\OopTest\Composition\Models\Licence;
use Me\OopTest\Composition\Repositories\OfficialLicenceRepository;
use Me\OopTest\Composition\Repositories\CustomLicenceRepository;
use Me\OopTest\Composition\Repositories\LicenceRepository;

class LicenceFactory
{
	public static function create(int $licenceType): Licence
	{
		if ($licenceType === LicenceRepository::OFFICIAL) {
			return new Licence(new OfficialLicenceRepository());
		}
		else if ($licenceType === LicenceRepository::CUSTOM) {
			return new Licence(new CustomLicenceRepository());
		}
	}
}