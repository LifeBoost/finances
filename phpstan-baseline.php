<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property App\\\\Domain\\\\Transaction\\\\Transaction\\:\\:\\$sourceWallet\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Domain/Transaction/Transaction.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property App\\\\Domain\\\\Transaction\\\\Transaction\\:\\:\\$targetWallet\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Domain/Transaction/Transaction.php',
];
$ignoreErrors[] = [
	'message' => '#^Unsafe usage of new static\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/SharedKernel/Id.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Symfony\\\\Component\\\\Validator\\\\ConstraintViolationListInterface\\:\\:getIterator\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/UI/API/ErrorHandlerMiddleware.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
