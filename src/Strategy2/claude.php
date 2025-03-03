<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// A flexible token and course management system using PHP. 
// This seems like a good case for using Strategy and Decorator patterns for token validation, along with Repository pattern for data access.

// Interfaces
interface TokenInterface {
    public function getCategory(): string;
    public function getValue(): int;
    public function canBeUsedFor(CourseInterface $course): bool;
}

interface CourseInterface {
    public function getId(): int;
    public function getCategory(): string;
    public function getCost(): int;
}

interface TokenValidatorInterface {
    public function validate(TokenInterface $token, CourseInterface $course): bool;
}

// Base Classes
abstract class Token implements TokenInterface {
    protected int $value;
    protected string $category;
    protected array $allowedCourseIds = [];
    protected array $allowedCourseCategories = [];
    protected bool $isUniversal = false;

    public function getValue(): int {
        return $this->value;
    }

    public function getCategory(): string {
        return $this->category;
    }

    public function canBeUsedFor(CourseInterface $course): bool {
        if ($this->isUniversal) {
            return true;
        }

        if (in_array($course->getId(), $this->allowedCourseIds)) {
            return true;
        }

        return in_array($course->getCategory(), $this->allowedCourseCategories);
    }
}

abstract class Course implements CourseInterface {
    protected int $id;
    protected string $category;
    protected int $cost;

    public function getId(): int {
        return $this->id;
    }

    public function getCategory(): string {
        return $this->category;
    }

    public function getCost(): int {
        return $this->cost;
    }
}

// Concrete Classes
class SpecificCourseToken extends Token {
    public function __construct(int $value, string $category, array $allowedCourseIds) {
        $this->value = $value;
        $this->category = $category;
        $this->allowedCourseIds = $allowedCourseIds;
    }
}

class CategoryToken extends Token {
    public function __construct(int $value, string $category, array $allowedCourseCategories) {
        $this->value = $value;
        $this->category = $category;
        $this->allowedCourseCategories = $allowedCourseCategories;
    }
}

class UniversalToken extends Token {
    public function __construct(int $value, string $category) {
        $this->value = $value;
        $this->category = $category;
        $this->isUniversal = true;
    }
}

// Token Validator Strategy Pattern
class BasicTokenValidator implements TokenValidatorInterface {
    public function validate(TokenInterface $token, CourseInterface $course): bool {
        return $token->canBeUsedFor($course) && $token->getValue() >= $course->getCost();
    }
}

// Repository Pattern
interface TokenRepositoryInterface {
    public function findByUser(int $userId): array;
}

interface CourseRepositoryInterface {
    public function findById(int $courseId): ?CourseInterface;
    public function findByCategory(string $category): array;
}


class SimpleCourse extends Course {
    public function __construct(int $id, string $category, int $cost) {
        $this->id = $id;
        $this->category = $category;
        $this->cost = $cost;
    }
}

class CourseRepository implements CourseRepositoryInterface {
    private ?PDO $db;

    public function __construct(PDO $db = null) {
        $this->db = $db;
    }

    public function findById(int $courseId): ?CourseInterface {
        // Mock implementation returning sample data
        $courses = [
            1 => new SimpleCourse(1, 'programming', 75),
            2 => new SimpleCourse(2, 'programming', 100),
            3 => new SimpleCourse(3, 'design', 150),
            4 => new SimpleCourse(4, 'business', 200),
            5 => new SimpleCourse(5, 'marketing', 125)
        ];

        return $courses[$courseId] ?? null;
    }

    public function findByCategory(string $category): array {
        // Mock implementation returning sample data
        $allCourses = [
            new SimpleCourse(1, 'programming', 75),
            new SimpleCourse(2, 'programming', 100),
            new SimpleCourse(3, 'design', 150),
            new SimpleCourse(4, 'business', 200),
            new SimpleCourse(5, 'marketing', 125)
        ];

        return array_filter($allCourses, function($course) use ($category) {
            return $course->getCategory() === $category;
        });
    }
}

class TokenRepository implements TokenRepositoryInterface {
    private ?PDO $db;

    public function __construct(PDO $db = null) {
        $this->db = $db;
    }

    public function findByUser(int $userId): array {
        // In a real implementation, this would query the database
        // Here we return mock data for demonstration
        return [
            new SpecificCourseToken(
                100, // value
                'PREMIUM', // token category
                [1, 2, 3] // allowed course IDs
            ),
            new CategoryToken(
                50, // value
                'BASIC', // token category
                ['programming', 'design'] // allowed course categories
            ),
            new UniversalToken(
                200, // value
                'VIP' // token category
            ),
            new SpecificCourseToken(
                75, // value
                'STANDARD', // token category
                [4, 5] // allowed course IDs
            ),
            new CategoryToken(
                150, // value
                'PREMIUM', // token category
                ['business', 'marketing'] // allowed course categories
            )
        ];
    }
}


// Token Service - Facade Pattern
class TokenService {
    private TokenRepositoryInterface $tokenRepository;
    private CourseRepositoryInterface $courseRepository;
    private TokenValidatorInterface $tokenValidator;

    public function __construct(
        TokenRepositoryInterface $tokenRepository,
        CourseRepositoryInterface $courseRepository,
        TokenValidatorInterface $tokenValidator
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->courseRepository = $courseRepository;
        $this->tokenValidator = $tokenValidator;
    }

    public function getAvailableTokensForCourse(int $userId, int $courseId): array {
        $tokens = $this->tokenRepository->findByUser($userId);
        $course = $this->courseRepository->findById($courseId);
        
        if (!$course) {
            throw new Exception('Course not found');
        }

        // return array_filter($tokens, function($token) use ($course) {
        //     return $this->tokenValidator->validate($token, $course);
        // });
        $validTokens = [];
        foreach ($tokens as $token) {
            if ($this->tokenValidator->validate($token, $course)) {
                $validTokens[] = $token;
            }
        }
        return $validTokens;
    }
}

class Example {
    public function demonstrateUsage() {
        // Initialize dependencies
        $tokenRepository = new TokenRepository();
        $courseRepository = new CourseRepository();
        $tokenValidator = new BasicTokenValidator();

        // Create token service
        $tokenService = new TokenService($tokenRepository, $courseRepository, $tokenValidator);

        // Get available tokens for course ID 1 (the PHP programming course that costs 75)
        try {
            $availableTokens = $tokenService->getAvailableTokensForCourse(1, 1);  // Changed 100 to 1
            foreach ($availableTokens as $token) {
                echo "Available token: {$token->getValue()} in category {$token->getCategory()}\n";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

$example = new Example();
$example->demonstrateUsage();

// This design implements several key patterns and features:

// 1. **Interface Segregation**:
//    - `TokenInterface` and `CourseInterface` define clear contracts
//    - Separate interfaces for repositories allow for different implementations

// 2. **Strategy Pattern**:
//    - `TokenValidatorInterface` allows for different validation strategies
//    - Easy to add new validation rules without modifying existing code

// 3. **Repository Pattern**:
//    - Abstracts data access through `TokenRepository` and `CourseRepository`
//    - Makes the system database-agnostic

// 4. **Token Types**:
//    - Base abstract `Token` class with shared functionality
//    - Specific implementations for different token types (Universal, Category, SpecificCourse)

// 5. **Facade Pattern**:
//    - `TokenService` provides a simple interface to complex token validation logic
//    - Handles coordination between repositories and validators

// 6. **Type Safety**:
//    - Strong typing throughout the system
//    - Clear method signatures and return types
