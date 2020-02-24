<?php

namespace vnh\counters;

use Clue\React\Buzz\Browser;
use function vnh\random_user_agent;

abstract class Counter {
	public $args;
	public $headers;
	public $counter;
	/**
	 * @var Browser
	 */
	public $client;

	public function __construct($args, $client) {
		$this->headers = [
			'User-Agent' => random_user_agent(),
		];
		$this->args = $args;
		$this->client = $client;

		$this->get_counter();
	}

	abstract public function get_counter();

	public function number_shorten($number, $precision = 1, $divisors = null) {
		// Setup default $divisors if not provided
		if (!isset($divisors)) {
			$divisors = [
				1000 ** 0 => '', // 1000^0 == 1
				1000 ** 1 => 'K', // Thousand
				1000 ** 2 => 'M', // Million
				1000 ** 3 => 'B', // Billion
				1000 ** 4 => 'T', // Trillion
				1000 ** 5 => 'Qa', // Quadrillion
				1000 ** 6 => 'Qi', // Quintillion
			];
		}

		// Loop through each $divisor and find the
		// lowest amount that matches
		foreach ($divisors as $divisor => $shorthand) {
			if (abs($number) < $divisor * 1000) {
				// We found a match!
				break;
			}
		}

		if (abs($number) < 1000) {
			$precision = 0;
		}

		// We found our match, or there were no matches.
		// Either way, use the last defined value for $divisor.
		return number_format($number / $divisor, $precision) . $shorthand;
	}
}
