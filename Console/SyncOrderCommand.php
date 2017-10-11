<?php
namespace Gracious\Interconnect\Console;

use Magento\Framework\App\State;
use Psr\Log\LoggerInterface as Logger;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\App\ObjectManager;
use Gracious\Interconnect\Http\Request\Client;
use Magento\Catalog\Helper\Image as ImageHelper;
use Symfony\Component\Console\Input\InputOption;
use Gracious\Interconnect\Console\CommandAbstract;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Gracious\Interconnect\Http\Request\Data\Order\Factory as OrderDataFactory;


class SyncOrderCommand extends CommandAbstract
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * SyncOrderCommand constructor.
     * @param State $state
     * @param Logger $logger
     * @param Client $client
     * @param OrderRepository $orderRepository
     */
    public function __construct(State $state, Logger $logger,  Client $client, OrderRepository $orderRepository)
    {
        parent::__construct($state, $logger, $client);

        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('interconnect:syncorder')->setDescription('Send an order to the Interconnect webservice');
        $this->addOption('id',null, InputOption::VALUE_REQUIRED, 'orderId', null);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $order = $this->orderRepository->get($input->getOption('id'));

        if($order === null) {
            $output->writeln('No order found, aborting....');
        }

        $orderDataFactory = new OrderDataFactory();
        $requestData = $orderDataFactory->setupData($order);
        $this->client->sendData($requestData, Client::ENDPOINT_ORDER);
    }
}