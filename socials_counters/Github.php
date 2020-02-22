<?php

namespace vnh\socials_counters;

use Psr\Http\Message\ResponseInterface;
use function vnh\random_user_agent;
use function vnh\request;

class Github extends Counter {
	public $counter = 'Github';
	public $base_url = 'https://api.github.com/users';

	public function get_counter() {
		$cached_counter = get_transient($this->args['transient_name']);

		if (!empty($cached_counter)) {
			$this->counter = $cached_counter;

			return true;
		}

		$url = sprintf('%s/%s', $this->base_url, $this->args['username']);

		$this->client->get($url, $this->headers)->then(function (ResponseInterface $results) {
			if ($results->getStatusCode() === 429) {
				set_transient($this->args['transient_name'], $this->counter, HOUR_IN_SECONDS);

				return false;
			}

			$array = json_decode($results->getBody(), true);
			if ($results->getStatusCode() === 200 && !empty($array['followers'])) {
				$this->counter = $array['followers'];
				$this->counter = $this->number_shorten($this->counter);
				set_transient($this->args['transient_name'], $this->counter, DAY_IN_SECONDS * 7);
			}
		});
	}
}
