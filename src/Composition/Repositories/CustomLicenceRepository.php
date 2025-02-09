<?php

// /src/Repositories/CustomLicenceRepository.php

namespace Me\OopTest\Composition\Repositories;

/**
 * Custom licences created by company
 */
class CustomLicenceRepository extends LicenceRepository
{	
	// Dummy licence data
	protected $licences = [
		1 => [
			'id' => 1,
			'name' => 'Security Card',
			'valid_from' => '2020-01-01',
			'validity_to' => '2020-12-31',
			'companyId' => 100
		],
		2 => [
			'id' => 2,
			'name' => 'Parking Permit',
			'valid_from' => '2020-01-01',
			'validity_to' => '2020-12-31',
			'companyId' => 100
		]
	];

	public function save(array $data)
	{
		$this->licences[] = $data;
	}

	public function fetchAll(int $companyId): array
	{
		return array_filter($this->licences, function($licence) use ($companyId) {
			return $licence['companyId'] == $companyId;
		});
	}
}