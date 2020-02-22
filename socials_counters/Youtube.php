<?php

namespace vnh\socials_counters;

use Psr\Http\Message\ResponseInterface;

class Youtube extends Counter {
	public $counter = 'Youtube';
	public $base_url = 'https://www.googleapis.com/youtube/v3/channels';
	public $api_key = 'AIzaSyBAwpfyAadivJ6EimaAOLh-F1gBeuwyVoY';

	public function get_counter() {
		$cached_counter = get_transient($this->args['transient_name']);

		if (!empty($cached_counter)) {
			$this->counter = $cached_counter;

			return true;
		}

		$check_channel_id = preg_match(
			'/(?:https|http)\:\/\/(?:[\w]+\.)?youtube\.com\/(?:c\/|channel\/|user\/)?([\w-]+)/',
			$this->args['channel_url'],
			$matches
		);

		if ($check_channel_id !== 1) {
			return false;
		}

		$channel_id = $matches[1];

		$url = add_query_arg(['part' => 'statistics', 'id' => $channel_id, 'key' => $this->api_key], $this->base_url);

		$this->client->get($url, $this->headers)->then(function (ResponseInterface $results) {
			if ($results->getStatusCode() === 429) {
				set_transient($this->args['transient_name'], $this->counter, HOUR_IN_SECONDS);

				return false;
			}

			$array = json_decode($results->getBody(), true);
			if ($results->getStatusCode() === 200 && !empty($array['items'][0]['statistics']['subscriberCount'])) {
				$this->counter = $array['items'][0]['statistics']['subscriberCount'];
				$this->counter = $this->number_shorten($this->counter);
				set_transient($this->args['transient_name'], $this->counter, DAY_IN_SECONDS * 7);
			}
		});
	}
}
