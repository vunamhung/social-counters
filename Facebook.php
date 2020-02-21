<?php

namespace core\socials_counter;

use Psr\Http\Message\ResponseInterface;

class Facebook extends Counter {
	public $base_url = 'https://www.facebook.com/plugins/likebox.php';
	public $counter = 'Facebook';

	public function get_counter() {
		$cached_counter = get_transient($this->args['transient_name']);

		if (!empty($cached_counter)) {
			$this->counter = $cached_counter;

			return true;
		}

		$url = add_query_arg(
			[
				'href' => $this->args['page_url'],
				'show_faces' => false,
				'header' => false,
				'stream' => false,
				'show_border' => false,
				'locale' => 'en_US',
			],
			$this->base_url
		);

		$this->client->get($url, $this->headers)->then(function (ResponseInterface $results) {
			if ($results->getStatusCode() === 429) {
				set_transient($this->args['transient_name'], $this->counter, HOUR_IN_SECONDS);

				return false;
			}

			if ($results->getStatusCode() === 200 && preg_match('/_1dr.*?>([\d,\.]+)/', (string) $results->getBody(), $matches) === 1) {
				$this->counter = $matches[1];
				$this->counter = str_replace(['.', ','], '', $this->counter);
				$this->counter = $this->number_shorten($this->counter);
				set_transient($this->args['transient_name'], $this->counter, DAY_IN_SECONDS * 7);
			}
		});
	}
}
