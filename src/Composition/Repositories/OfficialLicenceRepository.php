<?php

// /src/Repositories/OfficialLicenceRepository.php

namespace Me\OopTest\Composition\Repositories;

/**
 * Licences available in the system by country locale
 */
class OfficialLicenceRepository extends LicenceRepository
{
	// Dummy licence data
	protected $licences = [
		1 => [
			'id' => 1,
			'name' => 'Licence AAA',
			'valid_from' => '2020-01-01',
			'validity_to' => '2020-12-31',
			'countryLocale' => 'fi'
		],
		2 => [
			'id' => 2,
			'name' => 'Hygeine Pass',
			'valid_from' => '2020-01-01',
			'validity_to' => '2020-12-31',
			'countryLocale' => 'fi'
		]
	];

	public function save(array $data)
	{
		$this->licences[] = $data;
	}

	public function fetchAll(int $companyId): array
	{
		$countryLocale = $this->getCompanyLocale($companyId);

		return array_filter($this->licences, function($licence) use ($countryLocale) {
			return $licence['countryLocale'] == $countryLocale;
		});
	}

	protected function getCompanyLocale(int $companyId): string
	{
		// Dummy data
		$companyLocales = [
			100 => 'fi',
			101 => 'en',
			102 => 'de'
		];

		return $companyLocales[$companyId];
	}

}