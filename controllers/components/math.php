<?php
class MathComponent extends Object {
	function doComplexOperation($amount1, $amount2) {
		return $amount1 + $amount2;
	}

	function doUberComplexOperation ($amount1, $amount2) {
		$userInstance = ClassRegistry::init('User');
		$totalUsers = $userInstance->find('count');
		return ($amount1 + $amount2) / $totalUsers;
	}

}
?>