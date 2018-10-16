<?php
namespace App\Helpers;

use Cz\Git\GitRepository;

class GitRepositoryHelper extends GitRepository
{
    /**
     * @param $name
     *
     * @return GitRepository
     * @throws \Cz\Git\GitException
     */
    public function removeBranch($name)
    {
        return $this->begin()
            ->run('git branch', array(
                '-D' => $name,
            ))
            ->end();
    }
}
