<?php
namespace Smetana\Project\Helper;

use Magento\User\Model\User;
use Magento\Framework\App\Helper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Backend\Model\Auth\SessionFactory;
use Smetana\Project\Block\Options;

/**
 * Helper Data class
 *
 * @package Smetana\Project\Helper
 */
class Data extends Helper\AbstractHelper
{
    /**
     * Current Admin user Model
     *
     * @var User
     */
    static $currentAdminUser;

    /**
     * Resource Connection instance
     *
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param Helper\Context $context
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Helper\Context $context,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * Retrieve current Admin user Model
     *
     * @param void
     *
     * @return User
     */
    public static function getAdminUser(): User
    {
        if (is_null(static::$currentAdminUser)) {
            static::$currentAdminUser = ObjectManager::getInstance()
                ->get(SessionFactory::class)
                ->create()
                ->getUser();
        }

        return static::$currentAdminUser;
    }

    /**
     * Retrieve Admin user Role name
     *
     * @param void
     *
     * @return string
     */
    public static function getUserRoleName(): string
    {
        /** @var User $user */
        $user = static::getAdminUser();

        return $user->getRole()->getRoleName();
    }

    /**
     * Check Admin User has Coordinator role
     *
     * @param void
     *s
     * @return bool
     */
    public static function isCoordinator(): bool
    {
        return static::getUserRoleName() == Options::COORDINATOR_ROLE_NAME;
    }

    /**
     * Check Admin User has Specialist role
     *
     * @param void
     *
     * @return bool
     */
    public static function isSpecialist(): bool
    {
        return static::getUserRoleName() == Options::SPECIALIST_ROLE_NAME;
    }

    /**
     * Check button disabled param
     *
     * @param void
     *
     * @return bool
     */
    public function isButtonDisabled(): bool
    {
        return !is_null($this->_request->getParam('disabled'));
    }

    /**
     * Add call-centre Initiator to order Model
     *
     * @param array $orderData
     * @param int $userId
     *
     * @return Data
     */
    public function addInitiatorToSpecificOrder(array $orderData, int $userId): Data
    {
        $columns = ['order_initiator'];
        if (is_null($orderData['order_primary_initiator'])) {
            $columns[] = 'order_primary_initiator';
        }

        $connection = $this->resourceConnection->getConnection();
        foreach ($columns as $column) {
            $connection->query("update sales_order_grid set {$column}={$userId} where entity_id={$orderData['entity_id']};");
        }

        return $this;
    }
}
