<?php

namespace vnh\socials_counters;

use Psr\Http\Message\ResponseInterface;

class Dribbble extends Counter {
	public $counter = 'Dribble';
	public $base_url = 'https://dribbble.com/%s';

	public function get_counter() {
		$cached_counter = get_transient($this->args['transient_name']);

		if (!empty($cached_counter)) {
			$this->counter = $cached_counter;

			return true;
		}

		$this->client->get(sprintf($this->base_url, $this->args['username']), $this->headers)->then(function (ResponseInterface $results) {
			if ($results->getStatusCode() === 429) {
				set_transient($this->args['transient_name'], $this->counter, HOUR_IN_SECONDS);

				return false;
			}

			$body = (string) $results->getBody();
			$preg_match = preg_match('/<span.*?class=".*?">Followers<\/span>\s*<span.*?class="count.*?">.*?([\d,\.]+)/', $body, $matches);

			if ($results->getStatusCode() === 200 && $preg_match === 1) {
				$this->counter = $matches[1];
				$this->counter = str_replace(['.', ','], '', $this->counter);
				$this->counter = $this->number_shorten($this->counter);

				set_transient($this->args['transient_name'], $this->counter, DAY_IN_SECONDS * 7);
			}
		});
	}
}
