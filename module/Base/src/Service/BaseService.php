<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

abstract class BaseService
{
    /**
	 * Performs compression which is the same as COMPRESS() in MySQL and returns compressed string
	 * @param string $data
	 * @return string
	 */
	public function compress($data) 
	{
		if (is_null($data) || (!is_string($data) && !is_numeric($data))) {
			return;
		}
		return pack('L', strlen($data)) . gzcompress($data);
	}
}
