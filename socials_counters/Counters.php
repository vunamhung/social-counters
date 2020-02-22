<?php

namespace vnh\socials_counters;

use Clue\React\Buzz\Browser;
use vnh\contracts\Initable;
use React\EventLoop\Factory;

class Counters implements Initable {
	public $counters = [];
	public $args = [
		'facebook' => [
			'transient_name' => 'vnh_prefix_facebook_likes_counter',
			'page_url' => 'https://www.facebook.com/unsplash',
		],
		'instagram' => [
			'transient_name' => 'vnh_prefix_instagram_likes_counter',
			'username' => 'unsplash',
		],
		'twitter' => [
			'transient_name' => 'vnh_prefix_twitter_followers_counter',
			'username' => 'unsplash',
		],
		'youtube' => [
			'transient_name' => 'vnh_prefix_youtube_subscribers_counter',
			'channel_url' => 'https://www.youtube.com/channel/UCG5QeDT5DKU8TX_FU1fHQ6Q',
		],
		'vimeo' => [
			'transient_name' => 'vnh_prefix_vimeo_subscribers_counter',
			'channel_url' => 'https://vimeo.com/channels/nicetype',
		],
		'dribbble' => [
			'transient_name' => 'vnh_prefix_dribbble_followers_counter',
			'username' => 'unsplash',
		],
		'behance' => [
			'transient_name' => 'vnh_prefix_behance_followers_counter',
			'username' => 'jeremycowart',
		],
		'github' => [
			'transient_name' => 'vnh_prefix_github_followers_counter',
			'username' => 'torvalds',
		],
		'pinterest' => [
			'transient_name' => 'vnh_prefix_pinterest_followers_counter',
			'username' => 'unsplash',
		],
		'soundcloud' => [
			'transient_name' => 'vnh_prefix_soundcloud_followers_counter',
			'username' => 'eminemofficial',
		],
		'steam' => [
			'transient_name' => 'vnh_prefix_steam_followers_counter',
			'username' => 'steammusic',
		],
	];

	public function __construct($args = []) {
		$this->args = wp_parse_args($args, $this->args);
		$this->init();
	}

	public function init() {
		$loop = Factory::create();
		$client = new Browser($loop);

		$facebook = new Facebook($this->args['facebook'], $client);
		$twitter = new Twitter($this->args['twitter'], $client);
		$youtube = new Youtube($this->args['youtube'], $client);
		$vimeo = new Vimeo($this->args['vimeo'], $client);
		$dribbble = new Dribbble($this->args['dribbble'], $client);
		$behance = new Behance($this->args['behance'], $client);
		$github = new Github($this->args['github'], $client);
		$pinterest = new Pinterest($this->args['pinterest'], $client);
		$soundcloud = new Soundcloud($this->args['soundcloud'], $client);
		$steam = new Steam($this->args['steam'], $client);

		$loop->run();

		$this->counters = [
			'facebook' => $facebook->counter,
			'twitter' => $twitter->counter,
			'youtube' => $youtube->counter,
			'vimeo' => $vimeo->counter,
			'dribbble' => $dribbble->counter,
			'behance' => $behance->counter,
			'github' => $github->counter,
			'pinterest' => $pinterest->counter,
			'soundcloud' => $soundcloud->counter,
			'steam' => $steam->counter,
		];
	}
}
