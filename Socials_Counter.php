<?php

namespace vnh;

use core\socials_counter\Counters;

class Socials_Counter extends Widget {
	public function config() {
		return [
			'id_base' => 'socials_counter',
			'classname' => 'widget-socials-counter',
			'name' => __('Socials Counter', 'vnh_textdomain'),
			'description' => __('Displays counter of your social networks', 'vnh_textdomain'),
			'fields' => [
				'title' => [
					'label' => __('Title:', 'vnh_textdomain'),
					'type' => 'text',
					'default' => esc_html__('Social Counter', 'vnh_textdomain'),
				],
				'facebook' => [
					'label' => __('Facebook Fan Page URL', 'vnh_textdomain'),
					'type' => 'text',
					'default' => 'https://www.facebook.com/unsplash',
				],
				'instagram' => [
					'label' => __('Instagram Username', 'vnh_textdomain'),
					'type' => 'text',
					'default' => 'unsplash',
				],
				'twitter' => [
					'label' => __('Twitter Username', 'vnh_textdomain'),
					'type' => 'text',
					'default' => 'unsplash',
				],
				'dribbble' => [
					'label' => __('Dribbble Username', 'vnh_textdomain'),
					'type' => 'text',
					'default' => 'unsplash',
				],
				'behance' => [
					'label' => __('Behance Username', 'vnh_textdomain'),
					'type' => 'text',
					'default' => 'jeremycowart',
				],
				'pinterest' => [
					'label' => __('Pinterest Username', 'vnh_textdomain'),
					'type' => 'text',
					'default' => 'unsplash',
				],
				'youtube' => [
					'label' => __('Youtube Channel URL', 'vnh_textdomain'),
					'type' => 'text',
					'default' => 'https://www.youtube.com/channel/UCG5QeDT5DKU8TX_FU1fHQ6Q',
				],
				'vimeo' => [
					'label' => __('Vimeo Channel URL', 'vnh_textdomain'),
					'type' => 'text',
					'default' => 'https://vimeo.com/channels/nicetype',
				],
				'github' => [
					'label' => __('Github Username', 'vnh_textdomain'),
					'type' => 'text',
					'default' => 'torvalds',
				],
				'steam' => [
					'label' => __('Steam Group Name', 'vnh_textdomain'),
					'type' => 'text',
					'default' => 'steammusic',
				],
				'soundcloud' => [
					'label' => __('Soundcloud Username', 'vnh_textdomain'),
					'type' => 'text',
					'default' => 'eminemofficial',
				],
			],
		];
	}

	public function widget($args, $instance) {
		$get_counters = new Counters([
			'facebook' => [
				'transient_name' => $this->id . '_facebook',
				'page_url' => $instance['facebook'],
			],
			'twitter' => [
				'transient_name' => $this->id . '_twitter',
				'username' => $instance['twitter'],
			],
			'youtube' => [
				'transient_name' => $this->id . '_youtube',
				'channel_url' => $instance['youtube'],
			],
			'vimeo' => [
				'transient_name' => $this->id . '_vimeo',
				'channel_url' => $instance['vimeo'],
			],
			'dribbble' => [
				'transient_name' => $this->id . '_dribbble',
				'username' => $instance['dribbble'],
			],
			'behance' => [
				'transient_name' => $this->id . '_behance',
				'username' => $instance['behance'],
			],
			'github' => [
				'transient_name' => $this->id . '_github',
				'username' => $instance['github'],
			],
			'pinterest' => [
				'transient_name' => $this->id . '_pinterest',
				'username' => $instance['pinterest'],
			],
			'soundcloud' => [
				'transient_name' => $this->id . '_soundcloud',
				'username' => $instance['soundcloud'],
			],
			'steam' => [
				'transient_name' => $this->id . '_steam',
				'username' => $instance['steam'],
			],
		]);

		$this->widget_start($args, $instance);

		echo '<div class="social-counter-wrap" >';

		foreach ($get_counters->counters as $network => $counter) {
			if ($network === 'youtube') {
				$follower_text = __('subscribers', 'vnh_textdomain');
			} else {
				$follower_text = __('followers', 'vnh_textdomain');
			}
			printf('<div class="%s-counter">%s%s %s</div>', $network, get_svg_icon('social--' . $network), $counter, $follower_text); //phpcs:disable
		}

		echo '</div>';

		$this->widget_end($args);
	}

	public function update($new_instance, $old_instance) {
		if ($old_instance['facebook'] !== $new_instance['facebook']) {
			delete_transient($this->id . '_facebook');
		}

		if ($old_instance['twitter'] !== $new_instance['twitter']) {
			delete_transient($this->id . '_twitter');
		}

		if ($old_instance['instagram'] !== $new_instance['instagram']) {
			delete_transient($this->id . '_instagram');
		}

		if ($old_instance['dribbble'] !== $new_instance['dribbble']) {
			delete_transient($this->id . '_dribbble');
		}

		if ($old_instance['behance'] !== $new_instance['behance']) {
			delete_transient($this->id . '_behance');
		}

		if ($old_instance['pinterest'] !== $new_instance['pinterest']) {
			delete_transient($this->id . '_pinterest');
		}

		if ($old_instance['youtube'] !== $new_instance['youtube']) {
			delete_transient($this->id . '_youtube');
		}

		if ($old_instance['vimeo'] !== $new_instance['vimeo']) {
			delete_transient($this->id . '_vimeo');
		}

		if ($old_instance['github'] !== $new_instance['github']) {
			delete_transient($this->id . '_github');
		}

		if ($old_instance['steam'] !== $new_instance['steam']) {
			delete_transient($this->id . '_steam');
		}

		if ($old_instance['soundcloud'] !== $new_instance['soundcloud']) {
			delete_transient($this->id . '_soundcloud');
		}

		return $new_instance;
	}
}
