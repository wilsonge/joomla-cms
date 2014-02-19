<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/stemmer.php';

/**
 * Test class for FinderIndexerStemmer.
 * Generated by PHPUnit on 2012-06-10 at 14:52:14.
 */
class FinderIndexerStemmerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Tests the getInstance method
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function testGetInstance()
	{
		$this->assertThat(
			FinderIndexerStemmer::getInstance('porter_en'),
			$this->isInstanceOf('FinderIndexerStemmerPorter_en'),
			'getInstance with param "porter_en" returns an instance of FinderIndexerStemmerPorter_en.'
		);
	}

	/**
	 * Tests the getInstance method with a non-existing parser
	 *
	 * @return  void
	 *
	 * @since   3.0
	 *
	 * @expectedException  Exception
	 */
	public function testGetInstance_noParser()
	{
		FinderIndexerStemmer::getInstance('noway');
	}
}
