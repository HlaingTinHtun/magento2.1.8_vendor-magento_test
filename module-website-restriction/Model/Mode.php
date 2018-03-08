<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Restriction modes dictionary
 *
 */
namespace Magento\WebsiteRestriction\Model;

class Mode
{
    const ALLOW_NONE = 0;

    const ALLOW_LOGIN = 1;

    const ALLOW_REGISTER = 2;

    const HTTP_200 = 0;

    const HTTP_503 = 1;

    const HTTP_302_LOGIN = 0;

    const HTTP_302_LANDING = 1;
}
