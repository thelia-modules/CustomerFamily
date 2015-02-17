<?php
/*************************************************************************************/
/*      This file is part of the module CustomerFamily                               */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CustomerFamily\Event;

/**
 * Class CustomerFamilyEvents
 * @package CustomerFamily\Event
 */
class CustomerFamilyEvents
{
    const CUSTOMER_CUSTOMER_FAMILY_UPDATE = "action.front.customer.customer.family.update";
    const CUSTOMER_FAMILY_CREATE = "action.admin.customer.family.create";
    const CUSTOMER_FAMILY_UPDATE = "action.admin.customer.family.update";
    const CUSTOMER_FAMILY_DELETE = "action.admin.customer.family.delete";
}
