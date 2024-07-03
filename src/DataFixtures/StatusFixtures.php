<?php

namespace App\DataFixtures;

use App\Entity\Status;
use App\Enum\TaskStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class StatusFixtures extends Fixture implements FixtureGroupInterface
{
    // @codeCoverageIgnoreStart

    public static function getGroups(): array
    {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (TaskStatus::cases() as $status) {
            $statusEntry = new Status();
            $statusEntry->setName($status->value);
            $manager->persist($statusEntry);
        }

        $manager->flush();
    }
    // @codeCoverageIgnoreEnd
}
