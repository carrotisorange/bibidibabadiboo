<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Service\DataTransformer;

use Base\Service\DataTransformer\Universal\Handler\ToCommon;
use Base\Service\DataTransformer\Universal\Handler\FromCommon;

class Universal extends DataTransformer
{
    public function __construct(ToCommon $handlerToCommon, FromCommon $handlerFromCommon)
    {
        parent::__construct($handlerToCommon, $handlerFromCommon);
    }
}