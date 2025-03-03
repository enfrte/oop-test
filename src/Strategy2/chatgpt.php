<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ChatGPT

// Prompt:
 
// I need to lookup tokens a user has to spend on courses. 
// There are multiple courses. 
// Courses are categorized. 
// Tokens are also categorized.  
// Tokens can be configured to be used on specified courses, or all courses, or courses in a category. 
// Show OOP design pattern recommendations in PHP. 

// Here's an OOP design approach using the **Strategy Pattern** 

// ### **Key Classes and Interfaces**  
	
// 1. **Token**  
//    - Represents a token with a category and rules for usage.  

// 2. **Course**  
//    - Represents a course with a category.  

// 3. **TokenUsageStrategy (Interface)**  
//    - Defines how a token can be used (specific courses, all courses, category-based).  

// 4. **Concrete Token Usage Strategies**  
//    - `AllCoursesStrategy`: Token applies to all courses.  
//    - `SpecificCoursesStrategy`: Token applies to specific courses.  
//    - `CategoryBasedStrategy`: Token applies to courses in a specific category.  

// 5. **TokenManager**  
//    - Handles checking token usage for a given course.  


// ### **Implementation Sketch**  

// #### **Token Usage Strategies**  

interface TokenUsageStrategy {
	public function canUseToken(Token $token, Course $course): bool;
}

class AllCoursesStrategy implements TokenUsageStrategy {
	public function canUseToken(Token $token, Course $course): bool {
		return true; // Token applies to all courses
	}
}

class SpecificCoursesStrategy implements TokenUsageStrategy {
	private array $allowedCourses;

	public function __construct(array $allowedCourses) {
		$this->allowedCourses = $allowedCourses;
	}

	public function canUseToken(Token $token, Course $course): bool {
		return in_array($course->getId(), $this->allowedCourses);
	}
}

class CategoryBasedStrategy implements TokenUsageStrategy {
	private string $allowedCategory;

	public function __construct(string $allowedCategory) {
		$this->allowedCategory = $allowedCategory;
	}

	public function canUseToken(Token $token, Course $course): bool {
		return $course->getCategory() === $this->allowedCategory;
	}
}


// #### Token and Course Classes

class Token {
	private string $category;
	private TokenUsageStrategy $usageStrategy;

	public function __construct(string $category, TokenUsageStrategy $usageStrategy) {
		$this->category = $category;
		$this->usageStrategy = $usageStrategy;
	}

	public function canBeUsedOn(Course $course): bool {
		return $this->usageStrategy->canUseToken($this, $course);
	}
}

class Course {
	private int $id;
	private string $category;

	public function __construct(int $id, string $category) {
		$this->id = $id;
		$this->category = $category;
	}

	public function getId(): int {
		return $this->id;
	}

	public function getCategory(): string {
		return $this->category;
	}
}

// #### Token Manager (Lookup System)

class TokenManager {
	/** @var Token[] */
	private array $tokens;

	public function __construct(array $tokens) {
		$this->tokens = $tokens;
	}

	// public function getValidTokensForCourse(Course $course): array {
	// 	return array_filter($this->tokens, fn(Token $token) => $token->canBeUsedOn($course));
	// }
	public function getValidTokensForCourse(Course $course): array {
		$validTokens = [];

		foreach ($this->tokens as $token) {
			if ($token->canBeUsedOn($course)) {
				$validTokens[] = $token;
			}
		}

		return $validTokens;
	}
}

// ### Usage Example

$course1 = new Course(1, "math");
$course2 = new Course(2, "science");

$token1 = new Token("gold", new AllCoursesStrategy());
$token2 = new Token("silver", new SpecificCoursesStrategy([1])); // Can only be used on course 1
$token3 = new Token("bronze", new CategoryBasedStrategy("science")); // Can be used on all science courses

$manager = new TokenManager([$token1, $token2, $token3]);

$validTokens = $manager->getValidTokensForCourse($course1); // Should return token1 and token2

echo '<pre>';
print_r($validTokens);
echo '</pre>';

// ### Why This Design?
// - **Strategy Pattern** makes it easy to add new token usage rules.  
// - **Encapsulation** ensures that `Token` objects determine their own validity.  
// - **Extensibility** allows for different token policies without modifying existing code.  

