<?php

namespace vnh\socials_counters;

use Psr\Http\Message\ResponseInterface;

class Soundcloud extends Counter {
	public $counter = 'Soundcloud';
	public $base_url = 'https://api.soundcloud.com/users/';
	public $consumer_key = '8bcccc3476eaa137a084c9f0c041915f';

	public function get_counter() {
		$cached_counter = get_transient($this->args['transient_name']);

		if (!empty($cached_counter)) {
			$this->counter = $cached_counter;

			return true;
		}

		$url = add_query_arg(['consumer_key' => $this->consumer_key], $this->base_url . $this->args['username']);

		$this->client->get($url, $this->headers)->then(function (ResponseInterface $results) {
			if ($results->getStatusCode() === 429) {
				set_transient($this->args['transient_name'], $this->counter, HOUR_IN_SECONDS);

				return false;
			}

			$array = json_decode($results->getBody(), true);
			if ($results->getStatusCode() === 200 && !empty($array['followers_count'])) {
				$this->counter = $array['followers_count'];
				$this->counter = $this->number_shorten($this->counter);
				set_transient($this->args['transient_name'], $this->counter, DAY_IN_SECONDS * 7);
			}
		});
	}
}
