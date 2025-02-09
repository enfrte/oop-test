<?php

/**
 * Main launch file for testing. 
 * 
 * An app that demonstrates the use of the Composition design pattern.
 * The app has a Licence feature. The app can create an official licence by country locale and a custom licence by company.
 * Official licences are recognised in the country locale and are available to all companies based in the country. 
 * Custom licences are created and recognised by the company.
 * There would be two tables for these types of licences. 
 * These licences would be issued to company employees and tracked in the app. 
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

use Me\OopTest\Composition\Factories\LicenceFactory;
use Me\OopTest\Composition\Repositories\LicenceRepository;

// Create a new official licence by country locale
$officialLicence = LicenceFactory::create( LicenceRepository::OFFICIAL );
$officialLicence->save([
	'id' => 3,
	'name' => 'Licence ADR',
	'valid_from' => '2020-01-01',
	'validity_to' => '2020-12-31',
	'countryLocale' => 'fi'
]);

// Create a new custom licence by company
$customLicence = LicenceFactory::create( LicenceRepository::CUSTOM );
$customLicence->save([
	'id' => 3,
	'name' => 'VIP pass',
	'valid_from' => '2020-01-01',
	'validity_to' => '2020-12-31',
	'companyId' => 100
]);

$officialLicenceList = $officialLicence->fetchAll(100);
$customLicenceList = $customLicence->fetchAll(100);

$singleOfficialLicence = $officialLicence->find(2);
$singleCustomLicence = $customLicence->find(2);

echo '<pre>';
print_r($officialLicenceList);
print_r($customLicenceList);
print_r($singleOfficialLicence);
print_r($singleCustomLicence);
echo '</pre>';
