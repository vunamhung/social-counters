<?php

namespace core\socials_counter;

use Psr\Http\Message\ResponseInterface;

class Behance extends Counter {
	public $counter = 'Behance';
	public $base_url = 'https://www.behance.net/v2/users';
	public $api_key = 'INekEPLWGFlXlfmWjjOZD79vWNaD1Nxj';

	public function get_counter() {
		$cached_counter = get_transient($this->args['transient_name']);

		if (!empty($cached_counter)) {
			$this->counter = $cached_counter;

			return true;
		}

		$url = add_query_arg(['api_key' => $this->api_key], sprintf('%s/%s', $this->base_url, $this->args['username']));

		$this->client->get($url, $this->headers)->then(function (ResponseInterface $results) {
			if ($results->getStatusCode() === 429) {
				set_transient($this->args['transient_name'], $this->counter, HOUR_IN_SECONDS);

				return false;
			}

			$array = json_decode($results->getBody(), true);
			if ($results->getStatusCode() === 200 && !empty($array['user']['stats']['followers'])) {
				$this->counter = $array['user']['stats']['followers'];
				$this->counter = $this->number_shorten($this->counter);
				set_transient($this->args['transient_name'], $this->counter, DAY_IN_SECONDS * 7);
			}
		});
	}
}
