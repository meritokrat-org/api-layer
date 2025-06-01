<?php

namespace App\Command;

use App\Entity\AdministrativeUnit;
use App\Enum\Katottg\CategoryEnum;
use App\Repository\KatottgRepository;
use App\Repository\AdministrativeUnitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'katottg:make-tree',
)]
class KatottgMakeTreeCommand extends Command
{
    private const int MAX_LEVEL = 5;
    private ProgressBar $progressBar;

    public function __construct(
        private readonly KatottgRepository $katottgRepository,
        private readonly AdministrativeUnitRepository $unitRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->progressBar = new ProgressBar($output, $this->katottgRepository->count());
        $this->progressBar->setFormat('debug');
        $this->progressBar->start();

        foreach ($this->resolveLevel() as $unit) {
            $this->entityManager->flush();
            $io->newLine();
            $io->success($unit);
        }

        $this->entityManager->flush();
        $this->progressBar->finish();

        return Command::SUCCESS;
    }

    public function resolveLevel(?AdministrativeUnit $parent = null, int $level = 1, array $path = []): Generator
    {
        foreach ($this->select($parent, $level) as $katottg) {
            if ($parent !== null && CategoryEnum::CityWithSpecialStatus === $parent->getCategory()) {
                $level = 5;
            }
            $code = $katottg[sprintf('level%d', $level)];
            $unit = $this->unitRepository->findOneBy(['code' => $code]);

            if (!$unit) {
                $unit = new AdministrativeUnit()
                    ->setCode($code)
                    ->setName($katottg['name'])
                    ->setCategory($katottg['category'])
                    ->setLevel($level)
                    ->setPath(new ArrayCollection($path));

                $this->entityManager->persist($unit);
            }

            if ($level < self::MAX_LEVEL) {
                foreach ($this->resolveLevel($unit, $level + 1, [...$path, $unit]) as $child) {
                    $child->setParent($unit);
                }
            }

            $this->progressBar->advance();

            yield $unit;
        }
    }

    protected function select(?AdministrativeUnit $parent = null, int $level = 1): Generator
    {
//        $parentId = $parent[sprintf('level%d', $level - 1)] ?? null;
        $parentId = $parent?->getCode();
        /** @var CategoryEnum $parentCategory */
        $parentCategory = $parent?->getCategory();

        $qb = $this->katottgRepository
            ->createQueryBuilder('katottg')
            ->select('katottg')
            ->orderBy('katottg.id', 'DESC');

        $qb = match (true) {
            $parent !== null && $parentCategory === CategoryEnum::CityWithSpecialStatus => $qb
                ->where('katottg.level1 = :id')
                ->andWhere('katottg.level5 is not null')
                ->setParameter('id', $parentId),
            !$parent && $level === 1 => $qb
                ->where('katottg.level1 is not null')
                ->andWhere('katottg.level2 is null'),
            $parent !== null && $level > 1 && $level < 5 => $qb
                ->where(sprintf('katottg.level%d = :id', $level - 1))
                ->andWhere(sprintf('katottg.level%d is not null', $level))
                ->andWhere(sprintf('katottg.level%d is null', $level + 1))
                ->setParameter('id', $parentId),
            $parent !== null && $level > 4 => $qb
                ->where(sprintf('katottg.level%d = :id', $level - 1))
                ->andWhere(sprintf('katottg.level%d is not null', $level))
                ->setParameter('id', $parentId),
        };

        foreach ($qb->getQuery()->getArrayResult() as $item) {
            yield $item;
        }
    }
}
