<?php 

// Error setup
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

interface FormData {
    public function getParticipantList() : array;
}

class TokenPreAllocation {
    private $validateTokenReservation;
    private $preAllocateToken;
    private $formData;

    public function __construct(
        ValidateTokenReservation $validateTokenReservation, 
        PreAllocateToken $preAllocateToken, 
        FormData $formData
    ) {
        $this->validateTokenReservation = $validateTokenReservation;
        $this->preAllocateToken = $preAllocateToken;
        $this->formData = $formData;
    }

    public function preAllocateTokens() {
        $this->validateTokenReservation->validate();
        $this->preAllocateToken->save();
        echo 'Done';
    }
    
}

class CompanyTokenAvailability {
    private $companyId = null;

    public function __construct($comapnyId) {
        $this->companyId = $comapnyId;
    }

    public function getAmount() {
        // Lookup token availability by company id
        return 10;
    }
}

class ValidateTokenReservation {
    private $companyTokenAvailability;
    private $totalTokensRequested;

    public function __construct(CompanyTokenAvailability $companyTokenAvailability, int $totalTokensRequested) {
        $this->companyTokenAvailability = $companyTokenAvailability;
        $this->totalTokensRequested = $totalTokensRequested;
    }

    public function validate() {
        if ($this->companyTokenAvailability->getAmount() < $this->totalTokensRequested) {
            echo"Not enough tokens available";
            throw new Exception("Not enough tokens available");
        }
        return true;
    }
}

class GeneralOnlineCourseFormData implements FormData {
    private $participantList = [];
    private $requestData = [];

    public function __construct(array $requestData) {
        $this->requestData = $requestData;
        $this->processRequestData();
    }

    private function processRequestData() : array {
        $rd = $this->requestData;
        // iterate over request data and store in participant list
        return $this->participantList = [
            100 => [
                'idoppias' => 1000,
                'courseTypes' => [116, 112],
                'tokenAllocation' => 1,
                'cpc' => 1,                
            ],
            101 => [
                'idoppias' => 1001,
                'courseTypes' => [116, 112],
                'tokenAllocation' => 1,
                'cpc' => 1,                                
            ]
        ];
    }

    public function getParticipantList() : array {
        return $this->participantList;
    }

    public function getTokenTotalRequested() : int {
        $totalTokensRequested = 0;
        
        foreach ($this->participantList as $participant) {
            $totalTokensRequested += $participant['tokenAllocation'];
        }

        return $totalTokensRequested;
    }
}

class AdrFormData implements FormData {
    private $participantList = [];
    private $requestData = [];

    public function __construct(array $requestData) {
        $this->requestData = $requestData;
        $this->processRequestData();
    }

    private function processRequestData() : array {
        $rd = $this->requestData;
        // iterate over request data and store in participant list
        return $this->participantList = [
            100 => [
                'idoppias' => 1000,
                'courseTypes' => [116, 112],
                'tokenAllocation' => 1,
                'cpc' => 1,                
            ],
            101 => [
                'idoppias' => 1001,
                'courseTypes' => [116, 112],
                'tokenAllocation' => 1,
                'cpc' => 1,                                
            ]
        ];
    }

    public function getParticipantList() : array {
        return $this->participantList;
    }

    public function getTokenTotalRequested() : int {
        $totalTokensRequested = 0;
        
        foreach ($this->participantList as $participant) {
            $totalTokensRequested += $participant['tokenAllocation'];
        }

        return $totalTokensRequested;
    }
}

// Saves participants token pre-allocations to the db  
class PreAllocateToken {
    private $participantList = [];

    public function __construct($participantList) {
        $this->participantList = $participantList;    
    }

    public function save(): bool {
        // Iterate over participant list and save tokens to db
        return true;
    }

}


// Setup
$formData = new GeneralOnlineCourseFormData([/* Dummy form data */]);
$participantList = $formData->getParticipantList();
$preAllocateToken = new PreAllocateToken($participantList);
$companyTokenAvailability = new CompanyTokenAvailability(400);
$validateTokenReservation = new ValidateTokenReservation($companyTokenAvailability, $formData->getTokenTotalRequested());
$tokenPreAllocation = new TokenPreAllocation(
    $validateTokenReservation,
    $preAllocateToken,
    $formData
);

// Finally
$tokenPreAllocation->preAllocateTokens();
