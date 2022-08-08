<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Truck\Customerapplication\Controller\Customerapplication;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
    * Recipient email config path
    */
    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';
    /**
    * @var \Magento\Framework\Mail\Template\TransportBuilder
    */
    protected $_transportBuilder;

    /**
    * @var \Magento\Framework\Translate\Inline\StateInterface
    */
    protected $inlineTranslation;

    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
    * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $storeManager;
    /**
    * @var \Magento\Framework\Escaper
    */
    protected $_escaper;

    /**
     * @var AddressInterfaceFactory
     */
    private $dataAddressFactory;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
    * @param \Magento\Framework\App\Action\Context $context
    * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager
    */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        AddressInterfaceFactory $dataAddressFactory,
        AddressRepositoryInterface $addressRepository,
        \Magento\Framework\Escaper $escaper
    ) {
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->dataAddressFactory = $dataAddressFactory;
        $this->addressRepository = $addressRepository;
        $this->_escaper = $escaper;
    }

    /**
     * Post user question
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $store = $this->storeManager->getStore();
        $storeId = $store->getStoreId();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($post['email']);// load customer by email address
        if(!$customer->getId()){
            $customer->setWebsiteId($websiteId)
            ->setStore($store)
            ->setFirstname($post['name'])
            ->setLastname($post['lname'])
            ->setEmail($post['email'])
            ->setPassword('dummypassword@123*#');
            $customer->save();

            $address = $this->dataAddressFactory->create();

            $address->setFirstname($post['name']);
            $address->setLastname($post['lname']);
            $address->setTelephone($post['phone']);
            $street[] = $post['orderno'];
            $address->setStreet($street);

            $regionId = 23;
            $customerId = $customer->getId(); 
            $address->setCity($post['item_name']);
            $address->setCountryId('SE');
            $address->setPostcode($post['zipcode']);
            //$address->setRegionId(1);
            $address->setIsDefaultShipping(1);
            $address->setIsDefaultBilling(1);
            $address->setCustomerId($customerId);
            try {
                $this->addressRepository->save($address);
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $scopeConfig = $objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
                $email = $scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $this->inlineTranslation->suspend();

                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($post);
                $error = false;
                $sender = [
                    'name' => $this->_escaper->escapeHtml('TTB Grossist'),
                    'email' => $this->_escaper->escapeHtml('info@ttbgrossist.com'),
                ];

                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE; 
                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier('4') // this code we have mentioned in the email_templates.xml
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    )
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($sender)
                    ->addTo($post['email'])
                    ->getTransport();

                    $transport->sendMessage();
                    $this->inlineTranslation->resume();
            } catch (\Exception $e) {
                $this->inlineTranslation->resume();
                $this->messageManager->addError(__('We can\'t process your request right now. Sorry, that\'s all we know.'.$e->getMessage())
                );
            }
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $email = $scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->inlineTranslation->suspend();

        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($post);
            $error = false;
            $sender = [
                'name' => $this->_escaper->escapeHtml('TTB Grossist'),
                'email' => $this->_escaper->escapeHtml('info@ttbgrossist.com'),
            ];

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE; 
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier('1') // this code we have mentioned in the email_templates.xml
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($sender)
                ->addTo('info@ttbgrossist.com')
                ->getTransport();

                $transport->sendMessage();
                $this->inlineTranslation->resume();
                $this->messageManager->addSuccess(
                    __('Tack för din ansökan Vi kommer inom kort kontakta dig.')
                );
                //$this->_redirect('*/*/');
                return;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(__('We can\'t process your request right now. Sorry, that\'s all we know.'.$e->getMessage())
            );
            //$this->_redirect('*/*/');
            //return;
        }
    }
}