# Params

A framework agnostic library for validating input parameters.

[![Build Status](https://travis-ci.org/Danack/Params.svg?branch=master)](https://travis-ci.org/Danack/Params)

# Installation

```composer require danack/params```


# Basic usage

Given a set of rules, the library will extract the appropriate values from a 'variable map' and validate that the values meet the defined rules:


```php
$params = [
  'limit' => [
    new CheckSetOrDefault(10, $variableMap),
    new IntegerInput(),
    new MinIntValue(0),
    new MaxIntValue(100),
  ],
  'offset' => [
    new CheckSetOrDefault(null, $variableMap),
    new SkipIfNull(),
    new MinIntValue(0),
    new MaxIntValue(1000000),
  ],
];

list($limit, $offset) = Params::validate($params);

```

That code will extract the 'limit' and 'offset values from the variable map and check that the limit is an integer between 0 and 100, and that offset is either not set, or must be an integer between 0 and 1,000,000.

If there are any validation problems a ValidationException will be thrown. The validation problems can be retrieved from ValidationException::getValidationProblems.

# Basic usage without exceptions

Alternatively, you can avoid using exceptions for flow control:

```php

$validator = new ParamsValidator();

$limit = $validator->validate('limit', [
    new CheckSetOrDefault(10, $variableMap),
    new IntegerInput(),
    new MinIntValue(0),
    new MaxIntValue(100),
]);

$offset = $validator->validate('offset', [
    new CheckSetOrDefault(null, $variableMap),
    new SkipIfNull(),
    new MinIntValue(0),
    new MaxIntValue(1000000),
]);

$errors = $validator->getValidationProblems();

if (count($errors) !== 0) {
    // return an error
    return [null, $errors];
}

// return an object with null 
return [new GetArticlesParams($order, $limit, $offset), null];
```

## So......what is a 'variable map'?

A variable map is a simple interface to allow input parameters to be represented in various ways. For web applications, the most common implementation to use will likely be the Psr7InputMap that allows reading of input variables from a PSR 7 request object.


## Tests

We have several tools that are run to improve code quality. Please run `sh runTests.sh` to run them all. 

Pull requests should have full unit test coverage. Preferably also full mutation coverage through infection.

# Future work

## Parameter location

Some people care whether a parameter is in the query string or body. This library currently doesn't support differentiating them. 

## PHP could be nicer

It would be very convenient to be able to pass a callable to have it called to instantiate an object. I miss you https://wiki.php.net/rfc/callableconstructors

## I dislike using arrays with keys that have meaning


Rather than passing the rules around as an array, where the keys have meaning, the library could encapsulate that into an object. However that would make the functionality harder to write, and not give that much extra safety.

```
class ParamRules
{
    /** @var string */
    private $inputName;

    /** @var \Params\Rule */
    private $rules;

    /**
     * ParamRules constructor.
     * @param string $inputName
     * @param Rule $rules
     */
    public function __construct(string $inputName, Rule $rules)
    {
        $this->inputName = $inputName;
        $this->rules = $rules;
    }
}
```

