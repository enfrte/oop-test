<?php

/**
 * Builder pattern example.
 * For when you want to create something with many optional parameters. 
 * Builder allows you to chain methods in any order and only set the parameters you want. Particularly if you need to create your object step-by-step.
 * If you need to add another parameter, you add it to the builder class and don't have to change the object creation throughout your code.
 * 
 * The magic happens in the build method. 
 * Methods have to return $this to allow chaining.
 */
class Product
{
	public string $name;
	public float $price;
	public string $description = ''; // provide defaults for optional parameters
	public string $category = '';
	// many more to come over time :)

	public function __construct(string $name, float $price, string $description, string $category)
	{
		$this->name = $name;
		$this->price = $price;
		$this->description = $description;
		$this->category = $category;
	}
}
 
class ProductBuilder
{
	private string $name;
	private float $price;
	private string $description;
	private string $category;

	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	public function setPrice(float $price): self
	{
		$this->price = $price;
		return $this;
	}

	public function setDescription(string $description): self
	{
		$this->description = $description;
		return $this;
	}

	public function setCategory(string $category): self
	{
		$this->category = $category;
		return $this;
	}

	public function build(): Product
	{
		return new Product(
			$this->name, 
			$this->price, 
			$this->description, 
			$this->category
		);
	}
}
 
// Example usage:
$builder = new ProductBuilder();
$product = $builder->setName('Laptop')
	->setPrice(1500.00)
	->setDescription('A high-end gaming laptop')
	->setCategory('Electronics')
	->build();

echo "Product Name: " . $product->name . "\n";
echo "Product Price: " . $product->price . "\n";
echo "Product Description: " . $product->description . "\n";
echo "Product Category: " . $product->category . "\n";

