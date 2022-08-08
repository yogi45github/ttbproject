<?php
namespace Vision\CustomerAttribute\Block;

use Psr\Log\LoggerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;


/**
 * Customer Additional Info block
 *
 */
class AdditionalInfo extends \Magento\Customer\Block\Account\Dashboard
{
	private $logger;
}