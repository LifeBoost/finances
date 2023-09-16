<?php declare(strict_types = 1);

$ignoreErrors = [];
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
