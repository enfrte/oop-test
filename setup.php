<?php 


$directories = [
	'/src/Models',
	'/src/Repositories',
	'/src/Factories'
];

$files = [
	'/src/Models/Employee.php',
	'/src/Models/Licence.php',
	'/src/Models/LicenceType.php',
	'/src/Models/OfficialLicenceType.php',
	'/src/Models/CustomLicenceType.php',
	'/src/Repositories/LicenceRepository.php',
	'/src/Repositories/OfficialLicenceRepository.php',
	'/src/Repositories/CustomLicenceRepository.php',
	'/src/Factories/LicenceFactory.php',
	'/src/main.php'
];

foreach ($directories as $directory) {
	if (!is_dir(__DIR__ . $directory)) {
		mkdir(__DIR__ . $directory, 0777, true);
	}
}

foreach ($files as $file) {
	if (!file_exists(__DIR__ . $file)) {
		file_put_contents(__DIR__ . $file, "<?php\n\n// $file\n");
	}
}