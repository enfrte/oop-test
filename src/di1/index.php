<?php 

// Error setup
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

interface FormData {
    public function getParticipantList() : array;
    public function getTokenTotalRequested() : int;
}

class TokenPreAllocation {
    private ValidateTokenReservation $validateTokenReservation;
    private PreAllocateToken $preAllocateToken;

    public function __construct(
        ValidateTokenReservation $validateTokenReservation, 
        PreAllocateToken $preAllocateToken
    ) {
        $this->validateTokenReservation = $validateTokenReservation;
        $this->preAllocateToken = $preAllocateToken;
    }

    public function preAllocateTokens() {
        $this->validateTokenReservation->validate();
        $this->preAllocateToken->save();
        echo 'Done';
    }
    
}

class CompanyTokenAvailability {
    private $companyId;

    public function __construct(int $companyId) {
        $this->companyId = $companyId;
    }

    public function getAmount() {
        // Lookup token availability by company id
        return 10;
    }
}

class ValidateTokenReservation {
    private CompanyTokenAvailability $companyTokenAvailability;
    private $totalTokensRequested;

    public function __construct(CompanyTokenAvailability $companyTokenAvailability, int $totalTokensRequested) {
        $this->companyTokenAvailability = $companyTokenAvailability;
        $this->totalTokensRequested = $totalTokensRequested;
    }

    public function validate() {
        if ($this->companyTokenAvailability->getAmount() < $this->totalTokensRequested) {
            echo "Not enough tokens available"; // Just for demo purposes
            throw new Exception("Not enough tokens available");
        }
        return true;
    }
}

class GeneralOnlineCourseFormData implements FormData {
    private $participantList = [];
    private $requestData; // http request

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
    private FormData $formData;

    public function __construct(FormData $formData) {
        $this->formData = $formData;
    }

    public function save(): bool {
        $participantList = $this->formData->getParticipantList();
        // Iterate over participant list and save tokens to DB
        return true;
    }
}

// Setup
$formData = new GeneralOnlineCourseFormData([/* Put the http request here */]);
$companyTokenAvailability = new CompanyTokenAvailability(400);
$validateTokenReservation = new ValidateTokenReservation($companyTokenAvailability, $formData->getTokenTotalRequested());
$preAllocateToken = new PreAllocateToken($formData);
$tokenPreAllocation = new TokenPreAllocation(
    $validateTokenReservation,
    $preAllocateToken
);

// Finally
$tokenPreAllocation->preAllocateTokens();
