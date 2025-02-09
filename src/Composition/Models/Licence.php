<?php

namespace Me\OopTest\Composition\Models;

use Me\OopTest\Composition\Repositories\LicenceRepository;

class Licence
{
	protected $licenceRepository;

	public function __construct(LicenceRepository $licenceRepository)
	{
		$this->licenceRepository = $licenceRepository;
	}

	public function save(array $data): void
	{
		// Maybe perform validation here
		
		$this->licenceRepository->save($data);
	}

	public function fetchAll($criteria): array
	{
		return $this->licenceRepository->fetchAll($criteria);
	}

	public function find(int $id)
	{
		return $this->licenceRepository->find($id);
	}
}