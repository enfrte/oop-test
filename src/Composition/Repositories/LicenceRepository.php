<?php

// /src/Repositories/LicenceRepository.php

namespace Me\OopTest\Composition\Repositories;

abstract class LicenceRepository
{
	const OFFICIAL = 1;
	const CUSTOM = 2;
	
	protected $licences = [];

	abstract public function save(array $data);

	public function find(int $id)
	{
		return array_filter($this->licences, function($licence) use ($id) {
			return $licence['id'] == $id;
		});
	}

	abstract public function fetchAll(int $companyId): array; 

}
