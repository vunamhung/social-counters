<?php

namespace core\socials_counter;

use Psr\Http\Message\ResponseInterface;
use function vnh\random_user_agent;

class Vimeo extends Counter {
	public $base_url = 'https://api.vimeo.com/channels';
	public $access_tokens = ['f8ef92f31c737be59223b8448c9c1045', 'e3ac7d85daf38bc652890e6d1dae9b06', 'f0971d776f10b153b9dea8b19f836f1e'];

	public function get_counter() {
		$cached_counter = get_transient($this->args['transient_name']);

		if (!empty($cached_counter)) {
			$this->counter = $cached_counter;
			return true;
		}

		$check_channel_id = preg_match(
			'/(?:https|http)\:\/\/(?:[\w]+\.)?vimeo\.com\/(?:c\/|channels\/|user\/)?([\w-]+)/',
			$this->args['channel_url'],
			$matches
		);

		if ($check_channel_id !== 1) {
			return false;
		}

		$channel_id = $matches[1];

		// Choose a token
		$current_access_token = false;

		foreach ($this->access_tokens as $token) {
			if (get_transient(sprintf('vnh_prefix_stat_%s', $token)) !== 'disabled') {
				$current_access_token = $token;
				break;
			}
		}

		if (!$current_access_token) {
			return false;
		}

		$headers = [
			'Authorization' => 'Bearer ' . $current_access_token,
			'User-Agent' => random_user_agent(),
			'Accept-Language' => 'en',
		];
		$url = sprintf('%s/%s', $this->base_url, $channel_id);

		$this->client->get($url, $headers)->then(function (ResponseInterface $results) use ($current_access_token) {
			// Temporary disable token if needed
			$remaining = (int) $results->getHeader('x-ratelimit-remaining')[0];

			if ($remaining < 2) {
				$reset = $results->getHeader('x-ratelimit-reset')[0];
				$reset = strtotime($reset) - time();

				if ($reset > 0) {
					set_transient(sprintf('vnh_prefix_stat_%s', $current_access_token), 'disabled', $reset);
				}
			}

			// Disable 1 hour if too many requests
			if ($results->getStatusCode() === 429) {
				set_transient($this->args['transient_name'], $this->counter, HOUR_IN_SECONDS);

				return false;
			}

			$array = json_decode($results->getBody(), true);
			if ($results->getStatusCode() === 200 && !empty($array['metadata']['connections']['users']['total'])) {
				$this->counter = $array['metadata']['connections']['users']['total'];
				$this->counter = $this->number_shorten($this->counter);
				set_transient($this->args['transient_name'], $this->counter, DAY_IN_SECONDS * 7);
			}
		});
	}
}
