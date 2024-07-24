<?php

namespace App\Command;

use App\Message\ProductImport;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:product-import',
    description: 'Test product import',
    aliases: ['app:product-import'],
    hidden: false
)]
class TestProductImportCommand extends Command
{
    public function __construct(protected MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // set test unique data
        $data = [
            [
                'code' => 'DFF2345',
                'sectionCode' => 'FGH4579',
                'name' => 'A product',
                'volume' => '1',
                'weight' => '2',
                'filters' => [
                    [
                        'code' => 'GKD6844',
                        'value' => 'FJL4056',
                        'unit' => 'FGF4353',
                    ],
                    [
                        'code' => 'FGT4579',
                        'value' => 'FJH4579',
                        'unit' => 'FDH4579',
                    ],
                    [
                        'code' => 'EKD6856',
                        'value' => 'JWD6386',
                        'unit' => 'LOD2346',
                    ]
                ],
            ],
            [
                'code' => 'DGE4957',
                'sectionCode' => 'FGH4579',
                'name' => 'B product',
                'volume' => '3',
                'weight' => '4',
                'filters' => [
                    [
                        'code' => 'GQD6838',
                        'value' => 'FPK4004',
                        'unit' => 'KRG4334',
                    ],
                    [
                        'code' => 'FPV4523',
                        'value' => 'PGF9300',
                        'unit' => 'PKK3489',
                    ],
                    [
                        'code' => 'LLD3949',
                        'value' => 'FGQ3455',
                        'unit' => 'LLF2067',
                    ]
                ],
            ],
            // add more products with unique data
            [
                'code' => 'GHD1234',
                'sectionCode' => 'FGH4579',
                'name' => 'C product',
                'volume' => '5',
                'weight' => '6',
                'filters' => [
                    // add more filters with unique data
                    [
                        'code' => 'GFD1234',
                        'value' => 'FJH1234',
                        'unit' => 'FGH1234',
                    ],
                    [
                        'code' => 'GFD1235',
                        'value' => 'FJH1235',
                        'unit' => 'FGH1235',
                    ],
                    [
                        'code' => 'GFD1236',
                        'value' => 'FJH1236',
                        'unit' => 'FGH1236',
                    ]
                ],
            ],
            // add more products with unique data
            [
                'code' => 'GHD1237',
                'sectionCode' => 'FGH4579',
                'name' => 'D product',
                'volume' => '7',
                'weight' => '8',
                'filters' => [
                    // add more filters with unique data
                    [
                        'code' => 'GFD1238',
                        'value' => 'FJH1238',
                        'unit' => 'FGH1238',
                    ],
                    [
                        'code' => 'GFD1239',
                        'value' => 'FJH1239',
                        'unit' => 'FGH1239',
                    ],
                    [
                        'code' => 'GFD1240',
                        'value' => 'FJH1240',
                        'unit' => 'FGH1240',
                    ]
                ],
            ],
            // add more products with unique data
            [
                'code' => 'GHD1241',
                'sectionCode' => 'FGH4579',
                'name' => 'E product',
                'volume' => '9',
                'weight' => '10',
                'filters' => [
                    // add more filters with unique data
                    [
                        'code' => 'GFD1242',
                        'value' => 'FJH1242',
                        'unit' => 'FGH1242',
                    ],
                    [
                        'code' => 'GFD1243',
                        'value' => 'FJH1243',
                        'unit' => 'FGH1243',
                    ],
                    [
                        'code' => 'GFD1244',
                        'value' => 'FJH1244',
                        'unit' => 'FGH1244',
                    ]
                ],
            ],
            // add more products with unique data
            [
                'code' => 'GHD1245',
                'sectionCode' => 'FGH4579',
                'name' => 'F product',
                'volume' => '11',
                'weight' => '12',
                'filters' => [
                    // add more filters with unique data
                    [
                        'code' => 'GFD1246',
                        'value' => 'FJH1246',
                        'unit' => 'FGH1246',
                    ],
                    [
                        'code' => 'GFD1247',
                        'value' => 'FJH1247',
                        'unit' => 'FGH1247',
                    ],
                    [
                        'code' => 'GFD1248',
                        'value' => 'FJH1248',
                        'unit' => 'FGH1248',
                    ]
                ],
            ],
            // add more products with unique data
            [
                'code' => 'GHD1249',
                'sectionCode' => 'FGH4579',
                'name' => 'G product',
                'volume' => '13',
                'weight' => '14',
                'filters' => [
                    // add more filters with unique data
                    [
                        'code' => 'GFD1250',
                        'value' => 'FJH1250',
                        'unit' => 'FGH1250',
                    ],
                    [
                        'code' => 'GFD1251',
                        'value' => 'FJH1251',
                        'unit' => 'FGH1251',
                    ],
                    [
                        'code' => 'GFD1252',
                        'value' => 'FJH1252',
                        'unit' => 'FGH1252',
                    ]
                ],
            ],
        ];

        foreach ($data as $item) {
            $this->bus->dispatch((new ProductImport())->setMessage(json_encode($item)));
        }

        return Command::SUCCESS;
    }
}
