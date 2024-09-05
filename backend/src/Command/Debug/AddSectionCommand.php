<?php

namespace App\Command\Debug;

use App\Document\Section\Section;
use App\Features\Section\Repository\SectionRepository;
use App\Helper\Enum\LocaleType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'debug:section-add',
    description: 'Тестируем разделы'
)]
class AddSectionCommand extends Command
{

    public function __construct(
        private readonly SectionRepository $repository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dm = $this->repository->getDocumentManager();

        $section1 = new Section();

        $section1
            ->setCode('121')
            ->setLocale(LocaleType::fromString('ru'))
            ->setName('Test1');

        $dm->persist($section1);

        $section2 = new Section();

        $section2
            ->setCode(122)
            ->setLocale(LocaleType::fromString('ru'))
            ->setName('Test2')
            ->setParent($section1);

        $dm->persist($section2);

        $section3 = new Section();

        $section3
            ->setCode(123)
            ->setLocale(LocaleType::fromString('ru'))
            ->setName('Test3')
            ->setParent($section2);

        $dm->persist($section3);

        $dm->flush();


        return Command::SUCCESS;
    }
}
