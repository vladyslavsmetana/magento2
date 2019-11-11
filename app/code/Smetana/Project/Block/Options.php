<?php
namespace Smetana\Project\Block;

/**
 * Option names class
 *
 * @package Smetana\Project\Block
 */
class Options
{
    /**
     * Product attribute set name
     *
     * @var String
     */
    const PRODUCT_ATTRIBUTE_SET = 'Call-Centre';

    /**
     * Specialist role name
     *
     * @var String
     */
    const SPECIALIST_ROLE_NAME = 'Call-center specialist';

    /**
     * Coordinator role name
     *
     * @var String
     */
    const COORDINATOR_ROLE_NAME = 'Call-center coordinator';

    /**
     * Path to Admin Order grid
     *
     * @var String
     */
    const PATH_TO_ORDER_GRID = 'sales/order/index';

    /**
     * Path to cleaning order initiator
     *
     * @var String
     */
    const PATH_TO_REMOVE_INITIATOR = 'smetana_project_admin/order/cleaninitiator';
}
