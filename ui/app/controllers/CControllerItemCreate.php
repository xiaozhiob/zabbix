<?php declare(strict_types=0);
/*
** Zabbix
** Copyright (C) 2001-2023 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


class CControllerItemCreate extends CController {

	protected function init(): void {
		$this->setPostContentType(self::POST_CONTENT_TYPE_JSON);
	}

	protected function checkInput(): bool {
		$fields = [
			'hostid'			=> 'required|id',
			'allow_traps'		=> 'db items.allow_traps',
			'authtype'			=> 'db items.authtype',
			'delay'				=> 'db items.delay',
			'delay_flex'		=> 'array',
			'description'		=> 'db items.description',
			'follow_redirects'	=> 'db items.follow_redirects',
			'headers'			=> 'array',
			'history'			=> 'db items.history',
			'history_mode'		=> 'int32',
			'http_proxy'		=> 'db items.http_proxy',
			'interfaceid'		=> 'id',
			'inventory_link'	=> 'db items.inventory_link',
			'ipmi_sensor'		=> 'db items.ipmi_sensor',
			'jmx_endpoint'		=> 'db items.jmx_endpoint',
			'key'				=> 'db items.key_',
			'logtimefmt'		=> 'db items.logtimefmt',
			'master_itemid'		=> 'id',
			'name'				=> 'db items.name',
			'output_format'		=> 'db items.output_format',
			'parameters'		=> 'array',
			'params'			=> 'db items.params',
			'password'			=> 'db items.password',
			'post_type'			=> 'db items.post_type',
			'posts'				=> 'db items.posts',
			'preprocessing'		=> 'array',
			'privatekey'		=> 'db items.privatekey',
			'publickey'			=> 'db items.publickey',
			'query_fields'		=> 'array',
			'request_method'	=> 'db items.request_method',
			'retrieve_mode'		=> 'db items.retrieve_mode',
			'snmp_oid'			=> 'db items.snmp_oid',
			'ssl_cert_file'		=> 'db items.ssl_cert_file',
			'ssl_key_file'		=> 'db items.ssl_key_file',
			'ssl_key_password'	=> 'db items.ssl_key_password',
			'status'			=> 'db items.status',
			'status_codes'		=> 'db items.status_codes',
			'tags'				=> 'array',
			'timeout'			=> 'db items.timeout',
			'trapper_hosts'		=> 'db items.trapper_hosts',
			'trends'			=> 'db items.trends',
			'trends_mode'		=> 'int32',
			'type'				=> 'db items.type',
			'units'				=> 'db items.units',
			'url'				=> 'db items.url',
			'username'			=> 'db items.username',
			'value_type'		=> 'db items.value_type',
			'valuemapid'		=> 'id',
			'verify_host'		=> 'db items.verify_host',
			'verify_peer'		=> 'db items.verify_peer'
		];

		$ret = $this->validateInput($fields);

		if ($ret) {
			$custom_intervals = $this->getInput('delay_flex', []);
			$ret = isValidCustomIntervals($custom_intervals);
		}

		if (!$ret) {
			$this->setResponse(
				new CControllerResponseData(['main_block' => json_encode([
					'error' => [
						'messages' => array_column(get_and_clear_messages(), 'message')
					]
				])])
			);
		}

		return $ret;
	}

	protected function checkPermissions(): bool {
		return $this->getUserType() == USER_TYPE_ZABBIX_ADMIN
			|| $this->getUserType() == USER_TYPE_SUPER_ADMIN;
	}

	public function doAction() {
		$output = [];
		$item = $this->getFormData();
		$result = API::Item()->create($item);
		$messages = array_column(get_and_clear_messages(), 'message');

		if ($result) {
			$output['success']['title'] = _('Item created');

			if ($messages) {
				$output['success']['messages'] = $messages;
			}
		}
		else {
			$output['error'] = [
				'title' => _('Cannot create item'),
				'messages' => $messages
			];
		}

		$this->setResponse(new CControllerResponseData(['main_block' => json_encode($output)]));
	}

	protected function getFormData(): array {
		$input = [
			'allow_traps' => DB::getDefault('items', 'allow_traps'),
			'authtype' => DB::getDefault('items', 'authtype'),
			'delay' => ZBX_ITEM_DELAY_DEFAULT,
			'delay_flex' => [],
			'description' => DB::getDefault('items', 'description'),
			'follow_redirects' => DB::getDefault('items', 'follow_redirects'),
			'headers' => [],
			'history' => DB::getDefault('items', 'history'),
			'hostid' => 0,
			'http_authtype' => ZBX_HTTP_AUTH_NONE,
			'http_password' => '',
			'http_proxy' => DB::getDefault('items', 'http_proxy'),
			'http_username' => '',
			'interfaceid' => 0,
			'inventory_link' => 0,
			'ipmi_sensor' => DB::getDefault('items', 'ipmi_sensor'),
			'jmx_endpoint' => DB::getDefault('items', 'jmx_endpoint'),
			'key' => '',
			'logtimefmt' => DB::getDefault('items', 'logtimefmt'),
			'master_itemid' => 0,
			'name' => '',
			'output_format' => DB::getDefault('items', 'output_format'),
			'parameters' => [],
			'params' => DB::getDefault('items', 'params'),
			'password' => DB::getDefault('items', 'password'),
			'post_type' => DB::getDefault('items', 'post_type'),
			'posts' => DB::getDefault('items', 'posts'),
			'preprocessing' => [],
			'privatekey' => DB::getDefault('items', 'privatekey'),
			'publickey' => DB::getDefault('items', 'publickey'),
			'query_fields' => [],
			'request_method' => DB::getDefault('items', 'request_method'),
			'retrieve_mode' => DB::getDefault('items', 'retrieve_mode'),
			'script' => '',
			'snmp_oid' => DB::getDefault('items', 'snmp_oid'),
			'ssl_cert_file' => DB::getDefault('items', 'ssl_cert_file'),
			'ssl_key_file' => DB::getDefault('items', 'ssl_key_file'),
			'ssl_key_password' => DB::getDefault('items', 'ssl_key_password'),
			'status' => DB::getDefault('items', 'status'),
			'status_codes' => DB::getDefault('items', 'status_codes'),
			'tags' => [],
			'timeout' => DB::getDefault('items', 'timeout'),
			'trapper_hosts' => DB::getDefault('items', 'trapper_hosts'),
			'trends' => DB::getDefault('items', 'trends'),
			'type' => DB::getDefault('items', 'type'),
			'units' => DB::getDefault('items', 'units'),
			'url' => '',
			'username' => DB::getDefault('items', 'username'),
			'value_type' => DB::getDefault('items', 'value_type'),
			'valuemapid' => 0,
			'verify_host' => DB::getDefault('items', 'verify_host'),
			'verify_peer' => DB::getDefault('items', 'verify_peer')
		];
		$this->getInputs($input, array_keys($input));
		$field_map = [];

		if ($this->hasInput('key')) {
			$field_map['key'] = 'key_';
		}

		if ($this->getInput('history_mode', ITEM_STORAGE_CUSTOM) == ITEM_STORAGE_OFF) {
			$input['history'] = ITEM_NO_STORAGE_VALUE;
		}

		if ($this->getInput('trends_mode', ITEM_STORAGE_CUSTOM) == ITEM_STORAGE_OFF) {
			$input['trends'] = ITEM_NO_STORAGE_VALUE;
		}

		if ($this->getInput('type') == ITEM_TYPE_HTTPAGENT) {
			$field_map['http_authtype'] = 'authtype';
			$field_map['http_username'] = 'username';
			$field_map['http_password'] = 'password';
		}

		if ($this->hasInput('tags')) {
			$tags = [];

			foreach ($input['tags'] as $tag) {
				if ($tag['tag'] !== '' || $tag['value'] !== '') {
					$tags[] = $tag;
				}
			}

			$input['tags'] = $tags;
		}

		if ($this->hasInput('delay_flex')) {
			$custom_intervals = $this->getInput('delay_flex', []);
			isValidCustomIntervals($custom_intervals);
			$input['delay'] = getDelayWithCustomIntervals($input['delay'], $custom_intervals);
		}

		$input = CArrayHelper::renameKeys($input, $field_map);
		$hosts = API::Host()->get([
			'output' => ['hostid', 'status'],
			'hostids' => $this->getInput('hostid'),
			'templated_hosts' => true,
			'editable' => true
		]);
		$item = ['hostid' => $input['hostid']];
		$item += getSanitizedItemFields($input + [
			'templateid' => '0',
			'flags' => ZBX_FLAG_DISCOVERY_NORMAL,
			'hosts' => $hosts
		]);

		return $item;
	}
}
