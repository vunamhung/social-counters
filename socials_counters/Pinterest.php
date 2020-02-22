<?php

namespace vnh\socials_counters;

class Pinterest extends Counter {
	public function add_shortcode() {
		add_shortcode('pinterest_followers_counter', [$this, 'callback']);
	}

	public function get_counter() {
		$cached_counter = get_transient($this->args['transient_name']);

		if (!empty($cached_counter)) {
			$this->counter = $cached_counter;
			return true;
		}

		$metas = get_meta_tags(sprintf('https://pinterest.com/%s', $this->args['username']));

		if (!empty($metas['pinterestapp:followers'])) {
			$this->counter = $this->number_shorten($metas['pinterestapp:followers']);
		} else {
			$this->counter = 'Pinterest';
		}

		set_transient($this->args['transient_name'], $this->counter, DAY_IN_SECONDS * 7);

		return $this->counter;
	}
}
