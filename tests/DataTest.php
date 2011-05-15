<?php
/**
 * Serialized - PHP Library for Serialized Data
 *
 * Copyright (C) 2010-2011 Tom Klingenberg, some rights reserved
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program in a file called COPYING. If not, see
 * <http://www.gnu.org/licenses/> and please report back to the original
 * author.
 *
 * @author Tom Klingenberg <http://lastflood.com/>
 * @version 0.2.5
 * @package Tests
 */

Namespace Serialized;

require_once(__DIR__.'/TestCase.php');

/**
 * Test multiple data
 */
class DataTest extends TestCase
{
	private function runDataFile($fileName)
	{
		$datas = require $fileName;
		if ($datas === 1)
			$datas = array();

		$parser = new Parser();
		printf("%s:\n", basename($fileName));
		foreach($datas as $index => $serialized) {
			printf(' % 2d ... ', $index + 1);
			$start = microtime(true);
			$parser->setSerialized($serialized);
			$parsed = $parser->getParsed();
			$passed = microtime(true) - $start;
			$nodes = 0;
			$arrays = 0;
			array_walk_recursive($parsed, function($item, $key) use (&$nodes, &$arrays) {
				$nodes++;
				($key==0) && ($item=='array') && $arrays++;
			});
			printf("%f (% 6d bytes / % 5d nodes / % 4d arrays)\n", $passed, strlen($serialized), $nodes, $arrays);
		}
	}

	private function dataTest($data) {
		$fileName = $this->getDataPath().'/'.$data.'.php';
		$this->assertLint($fileName);
		$this->runDataFile($fileName);
	}

	private function getDataPath() {
		return __DIR__.'/../data/serialize';
	}

	private function getData() {
		$path = $this->getDataPath();
		$mask = '??-*.php';
		return array_map(function($file){return substr(basename($file),0,-4);}, glob($path.'/'.$mask));
	}

	/**
	 * @group data
	 */
	public function testData() {
		$datas = $this->getData();
		foreach($datas as $data) {
			$this->dataTest($data);
		}
	}
}