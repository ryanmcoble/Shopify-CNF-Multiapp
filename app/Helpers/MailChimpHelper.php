<?php 
namespace App\Helpers;

class MailChimpHelper {

	private $apiUrl = '';
	private $username = '';
	private $apiKey = '';

	public function __construct($APIUrl, $username, $apiKey) {
		$this->apiUrl = $APIUrl;
		$this->username = $username;
		$this->apiKey = $apiKey;
	}

	// check if authenticated
	public function checkAuth() {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->apiKey);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		$output = json_decode(curl_exec($ch), TRUE);

		if(array_key_exists('status', $output) && $output['status'] != 200) {
			return false;
		}

		return true;
	}

	// check if a list already exists
	public function hasList($listName = '') {
		$remaining = 0;
		$count = 100;
		$offset = 0;
		$list = [];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'lists/?count=' . $count . '&offset=' . $offset);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->apiKey);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		$output = json_decode(curl_exec($ch), TRUE);

		$remaining = $output['total_items'];
		$remaining = $remaining - count($output['lists']);
		$list = array_merge($list, $output['lists']);

		// loop over pages
		while($remaining > 0) {
			$offset = $offset + $count;

			curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'lists/?count=' . $count . '&offset=' . $offset);
			$output = json_decode(curl_exec($ch), TRUE);

			if($output['lists']) {
				$list = array_merge($list, $output['lists']);
			}

			$remaining = $remaining - count($output['lists']);
		}

		curl_close($ch);

		for($i = 0; $i < count($list); $i++) {
			if($list[$i]['name'] != '' && $list[$i]['name'] == $listName) {
				return $list[$i];
			}
		}

		return false;
	}

	// check if a member is already in the list
	public function hasMember($listId, $email) {

		$remaining = 0;
		$count = 100;
		$offset = 0;
		$members = [];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'lists/' . $listId . '/members/');
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->apiKey);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		$output = json_decode(curl_exec($ch), TRUE);

		$remaining = $output['total_items'];
		$remaining = $remaining - count($output['members']);
		$members = array_merge($members, $output['members']);

		// loop over pages
		while($remaining > 0) {
			$offset = $offset + $count;

			curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'lists/' . $listId . '/members/?count=' . $count . '&offset=' . $offset);
			$output = json_decode(curl_exec($ch), TRUE);

			if($output['members']) {
				$members = array_merge($members, $output['members']);
			}

			$remaining = $remaining - count($output['members']);
		}

		for($i = 0; $i < count($members); $i++) {
			if($members[$i]['email_address'] != '' && $members[$i]['email_address'] == $email) {
				return $members[$i];
			}
		}

		return false;
	}

	// create new list
	public function createList($listName = '') {
		$listData = [
			'name' => $listName,
			'contact' => [
				'company' => 'Cute N Fuzzies, LLC',
				'address1' => '4154 Seigman Ave.',
				'address2' => '',
				'city' => 'Columbus',
				'zip' => '43213',
				'country' => '164',
				'phone' => '(614) 772-9886'
			],
			'permission_reminder' => 'You are receiving this email because you opted-in at our website. Thank you for your support.',
			'use_archive_bar' => true,
			'campaign_defaults' => [
				'from_name' => 'Cute N Fuzzies',
				'from_email' => 'ryan.m.coble@gmail.com',
				'subject' => $listName,
				'language' => 'en'
			],
			'notify_on_subscribe' => '',
			'notify_on_unsubscribe' => '',
			'email_type_option' => false,
			'visibility' => 'pub'
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'lists/');
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->apiKey);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, count($listData));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($listData));
		$output = json_decode(curl_exec($ch), TRUE);

		if(!array_key_exists('id', $output) || !$output['id']) {
			return false;
		}

		return $output;
	}

	// subscribe a member to a list
	public function subscribeMember($listId, $email, $userMeta = []) {
		$memberData = [
			'email_address' => $email,
			'email_type' => 'html',
			'status' => 'subscribed',
			'language' => 'en'
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'lists/' . $listId . '/members');
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->apiKey);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, count($memberData));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($memberData));
		$output = json_decode(curl_exec($ch), TRUE);

		if(!array_key_exists('id', $output) || $output['id']) {
			return false;
		}

		return $output;
	}
}