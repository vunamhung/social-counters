<?php

namespace vnh\socials_counters;

use Psr\Http\Message\ResponseInterface;
use function vnh\random_user_agent;

class Twitter extends Counter {
	public $counter = 'Twitter';
	public $base_url = 'https://api.twitter.com/1.1/users/show.json';
	public $access_token = 'AAAAAAAAAAAAAAAAAAAAAJBzagAAAAAAXr%2Fxj2UWtV%2BnQNigsUm%2Bjrlkr4o%3DoYt2AFQFvPpPsJ1wtVmJ3MLetbYnmTWLFzDZJWLnXZtRJRZKOQ';

	public function get_counter() {
		$cached_counter = get_transient($this->args['transient_name']);

		if (!empty($cached_counter)) {
			$this->counter = $cached_counter;

			return true;
		}

		$headers = [
			'Authorization' => 'Bearer ' . $this->access_token,
			'User-Agent' => random_user_agent(),
		];
		$url = add_query_arg(['screen_name' => $this->args['username']], $this->base_url);

		$this->client->get($url, $headers)->then(function (ResponseInterface $results) {
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
