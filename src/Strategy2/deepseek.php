<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// To design a system in PHP that handles tokens and courses with the requirements you've specified, you can use several Object-Oriented Programming (OOP) design patterns. Below is a recommendation using a combination of **Strategy Pattern**, **Composite Pattern**, and **Decorator Pattern**.

// ---

// ### 1. **Strategy Pattern**
// The **Strategy Pattern** is useful for defining different ways tokens can be used (e.g., on specific courses, all courses, or courses in a category). This allows you to encapsulate the logic for token usage in separate classes.

// ---

// ### 2. **Composite Pattern**
// The **Composite Pattern** is helpful for handling the hierarchical structure of courses and categories. It allows you to treat individual courses and categories uniformly.

// ---

// ### 3. **Decorator Pattern**
// The **Decorator Pattern** can be used to add additional behavior (e.g., validation or logging) to the token usage logic without modifying the core classes.

// ---

// ### PHP Implementation

// #### Step 1: Define the Course and Category Classes

class Course {
    protected $id;
    protected $name;
    protected $category;

    public function __construct($id, $name, $category) {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
    }

    public function getId() {
        return $this->id;
    }

    public function getCategory() {
        return $this->category;
    }
}

class Category {
    protected $id;
    protected $name;

    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId() {
        return $this->id;
    }
}


// #### Step 2: Define the Token Class

class Token {
    protected $id;
    protected $amount;
    protected $usageStrategy;

    public function __construct($id, $amount, TokenUsageStrategy $usageStrategy) {
        $this->id = $id;
        $this->amount = $amount;
        $this->usageStrategy = $usageStrategy;
    }

    public function canUseOnCourse(Course $course) {
        return $this->usageStrategy->canUseOnCourse($course);
    }

    public function getAmount() {
        return $this->amount;
    }
}


// #### Step 3: Define the Token Usage Strategies

interface TokenUsageStrategy {
    public function canUseOnCourse(Course $course);
}

class SpecificCourseUsage implements TokenUsageStrategy {
    protected $courseId;

    public function __construct($courseId) {
        $this->courseId = $courseId;
    }

    public function canUseOnCourse(Course $course) {
        return $course->getId() === $this->courseId;
    }
}

class AllCoursesUsage implements TokenUsageStrategy {
    public function canUseOnCourse(Course $course) {
        return true;
    }
}

class CategoryUsage implements TokenUsageStrategy {
    protected $categoryId;

    public function __construct($categoryId) {
        $this->categoryId = $categoryId;
    }

    public function canUseOnCourse(Course $course) {
        return $course->getCategory()->getId() === $this->categoryId;
    }
}

// #### Step 4: Use the Composite Pattern for Course Categories

interface CourseComponent {
    public function getId();
}

class CourseComposite implements CourseComponent {
    protected $id;
    protected $courses = [];

    public function __construct($id) {
        $this->id = $id;
    }

    public function addCourse(Course $course) {
        $this->courses[] = $course;
    }

    public function getId() {
        return $this->id;
    }

    public function getCourses() {
        return $this->courses;
    }
}


// #### Step 5: Example Usage

// Create courses and categories
$mathCategory = new Category(1, 'Mathematics');
$physicsCategory = new Category(2, 'Physics');

$course1 = new Course(101, 'Algebra', $mathCategory);
$course2 = new Course(102, 'Calculus', $mathCategory);
$course3 = new Course(201, 'Mechanics', $physicsCategory);

// Create tokens with different usage strategies
$token1 = new Token(1, 100, new SpecificCourseUsage(101)); // Can only be used on Algebra
$token2 = new Token(2, 200, new CategoryUsage(1)); // Can be used on any Math course
$token3 = new Token(3, 300, new AllCoursesUsage()); // Can be used on any course

// Check token usage
echo $token1->canUseOnCourse($course1) ? "Token 1 can be used on Algebra\n" : "Token 1 cannot be used on Algebra\n";
echo $token2->canUseOnCourse($course2) ? "Token 2 can be used on Calculus\n" : "Token 2 cannot be used on Calculus\n";
echo $token3->canUseOnCourse($course3) ? "Token 3 can be used on Mechanics\n" : "Token 3 cannot be used on Mechanics\n";

// ### Explanation
// 1. **Token Usage Strategies**: The `TokenUsageStrategy` interface and its implementations (`SpecificCourseUsage`, `AllCoursesUsage`, `CategoryUsage`) define how tokens can be used.
// 2. **Composite Pattern**: The `CourseComposite` class allows you to group courses into categories.
// 3. **Flexibility**: By using the Strategy Pattern, you can easily add new token usage rules without modifying existing code.

// This design is scalable and adheres to SOLID principles, making it easy to extend and maintain.