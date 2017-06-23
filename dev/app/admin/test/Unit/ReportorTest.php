<?php
namespace Admin\Test\Unit;

class ReportorTest extends \PHPUnit_Framework_TestCase
{

	public function testGetDrEntitySet()
	{
		$reportor_service = service('reportor');

		$start = '2016-03-25';
		$end = date('Y-m-d');
		$start_ts = strtotime($start);
		$end_ts = strtotime($end);

		$day_count = floor(($end_ts - $start_ts) / 86400);

		$set = $reportor_service->getDrEntitySet($start, $end);
		$set->setCountPerPage(0);

		$items = $set->getItems();
		$this->assertEquals($day_count, count($items));

		foreach ($items as $item) {
			$this->assertObjectHasAttribute('date', $item);
			$this->assertObjectHasAttribute('rqt_count', $item);
			$this->assertObjectHasAttribute('product_count', $item);
			$this->assertObjectHasAttribute('idea_count', $item);
			$this->assertObjectHasAttribute('feature_count', $item);
			$this->assertObjectHasAttribute('invent_count', $item);
			$this->assertObjectHasAttribute('created', $item);

			$date_ts = strtotime($item->date);
			$this->assertGreaterThanOrEqual($start_ts, $date_ts);
			$this->assertLessThan($end_ts, $date_ts);
		}
	}
}