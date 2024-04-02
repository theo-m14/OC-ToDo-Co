<?php

namespace App\Story;

use App\Factory\TaskFactory;
use Zenstruck\Foundry\Story;

final class TaskStory extends Story
{
    public function build(): void
    {
        TaskFactory::createMany(10);
    }
}
