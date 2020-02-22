<?php

namespace vnh\socials_counters;

use Psr\Http\Message\ResponseInterface;

class Steam extends Counter {
	public $counter = 'Steam';

	public function get_counter() {
		$cached_counter = get_transient($this->args['transient_name']);

		if (!empty($cached_counter)) {
			$this->counter = $cached_counter;

			return true;
		}

		$url = sprintf('https://steamcommunity.com/groups/%s/memberslistxml?xml=1', $this->args['username']);

		$this->client->get($url, $this->headers)->then(function (ResponseInterface $results) {
			if ($results->getStatusCode() === 429) {
				set_transient($this->args['transient_name'], $this->counter, HOUR_IN_SECONDS);

				return false;
			}

			$xml = simplexml_load_string($results->getBody());
			$json = wp_json_encode($xml);
			$array = json_decode($json, true);
			if ($results->getStatusCode() === 200 && !empty($array['groupDetails']['memberCount'])) {
				$this->counter = $array['groupDetails']['memberCount'];
				$this->counter = $this->number_shorten($this->counter);
				set_transient($this->args['transient_name'], $this->counter, DAY_IN_SECONDS * 7);
			}
		});
	}
}
