<?php
/**
 * @package     Joomla.Tests
 * @subpackage  Api.tests
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Codeception\Util\HttpCode;

/**
 * Class BannerCest.
 *
 * Basic com_banners (banner) tests.
 *
 * @since   4.0.0
 */
class BannerCest
{
	/**
	 * Api test before running.
	 *
	 * @param   mixed   ApiTester  $I  Api tester
	 *
	 * @return void
	 *
	 * @since   4.0.0
	 */
	public function _before(ApiTester $I)
	{
	}

	/**
	 * Api test after running.
	 *
	 * @param   mixed   ApiTester  $I  Api tester
	 *
	 * @return void
	 *
	 * @since   4.0.0
	 */
	public function _after(ApiTester $I)
	{
	}

	/**
	 * Test the crud endpoints of com_banners from the API.
	 *
	 * @param   mixed   ApiTester  $I  Api tester
	 *
	 * @return void
	 *
	 * @since   4.0.0
	 *
	 * @TODO: Make these separate tests but requires sample data being installed so there are existing banners
	 */
	public function testCrudOnBanner(ApiTester $I)
	{
		$I->amHttpAuthenticated('admin', 'admin');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/vnd.api+json');

		$testBanner = [
			'name' => 'My Custom Advert',
			'catid' => 3,
			'description' => '',
			'custombannercode' => '',
			'metakey' => '',
			'params' => [
				'imageurl' => '',
				'width' => '',
				'height' => '',
				'alt' => ''
			],
		];

		$I->sendPOST('/banners', $testBanner);

		$I->seeResponseCodeIs(HttpCode::OK);

		$I->amHttpAuthenticated('admin', 'admin');
		$I->haveHttpHeader('Accept', 'application/vnd.api+json');
		$I->sendGET('/banners/1');
		$I->seeResponseCodeIs(HttpCode::OK);

		$I->amHttpAuthenticated('admin', 'admin');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/vnd.api+json');
		$I->sendPATCH('/banners/1', ['name' => 'Different Custom Advert', 'state' => -2]);
		$I->seeResponseCodeIs(HttpCode::OK);

		$I->amHttpAuthenticated('admin', 'admin');
		$I->haveHttpHeader('Accept', 'application/vnd.api+json');
		$I->sendDELETE('/banners/1');
		$I->seeResponseCodeIs(HttpCode::NO_CONTENT);
	}

	/**
	 * Test the category crud endpoints of com_banners from the API.
	 *
	 * @param   mixed   ApiTester  $I  Api tester
	 *
	 * @return void
	 *
	 * @since   4.0.0
	 *
	 * @TODO: Make these separate tests but requires sample data being installed so there are existing categories
	 */
	public function testCrudOnCategory(ApiTester $I)
	{
		$I->amHttpAuthenticated('admin', 'admin');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/vnd.api+json');

		$testarticle = [
			'title' => 'A test category',
			'parent_id' => 3
		];

		$I->sendPOST('/banners/categories', $testarticle);

		$I->seeResponseCodeIs(HttpCode::OK);

		$I->amHttpAuthenticated('admin', 'admin');
		$I->haveHttpHeader('Accept', 'application/vnd.api+json');
		$I->sendGET('/banners/categories/8');
		$I->seeResponseCodeIs(HttpCode::OK);

		$I->amHttpAuthenticated('admin', 'admin');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/vnd.api+json');
		$I->sendPATCH('/banners/categories/8', ['title' => 'Another Title']);
		$I->seeResponseCodeIs(HttpCode::OK);

		$I->amHttpAuthenticated('admin', 'admin');
		$I->haveHttpHeader('Accept', 'application/vnd.api+json');
		$I->sendDELETE('/banners/categories/8');
		$I->seeResponseCodeIs(HttpCode::NO_CONTENT);
	}
}
