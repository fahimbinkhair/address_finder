<?php
declare(strict_types=1);
/**
 * Description:
 * download the postcode database from mysociety.org and load postcode, latitude and longitude in the DB
 * by default only changed/update file from {data-source}/Data/multi_csv will be processed by user can force to process
 * all file
 *
 * @package App\Controller
 */

namespace App\Command;

use App\Services\Postcode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class UpdatePostcodeCommand
 *
 * @package App\Command
 */
class UpdatePostcodeCommand extends Command
{
    /** @var string $defaultName name of this command */
    protected static $defaultName = 'app:update-postcode';

    /** @var Postcode $postcode */
    private $postcode;

    /**
     * UpdatePostcodeCommand constructor.
     * @param Postcode $postcode
     * @throws LogicException
     */
    public function __construct(Postcode $postcode)
    {
        $this->postcode = $postcode;
        parent::__construct();
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function configure()
    {
        $this->setDescription('Download the postcode database from mysociety.org and load postcode, latitude and longitude in the DB')
            ->addOption(
                'load-all-postcodes',
                'a',
                InputArgument::OPTIONAL,
                'By default only changed/update file from {data-source}/Data/multi_csv will be processed but this will force to process all file [y/n]',
                'n'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws InvalidArgumentException|\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /** @var string $loadAllPostcodes */
        $loadAllPostcodes = strtolower($input->getOption('load-all-postcodes'));

        if ($loadAllPostcodes === 'y') {
            $io->note('We are going to reload entire postcode database');
        } elseif ($loadAllPostcodes === 'n') {
            $io->note('We are going to update the postcode database');
        } else {
            throw new \Exception('Invalid value provided for the option load-all-postcodes');
        }

        if ($this->postcode->setLoadAllPostcodes($loadAllPostcodes === 'y')->loadPostcode()) {
            $io->success('Successfully reloaded/updated the postcode database');

            return 0;
        }

        $io->error($this->postcode->getFailureReason());
        return 0;
    }
}
