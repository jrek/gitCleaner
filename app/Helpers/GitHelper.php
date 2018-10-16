<?php

namespace App\Helpers;

use App\Helpers\GitRepositoryHelper;

class GitHelper
{
    /** @var GitRepositoryHelper */
    private $gitRepo;

    /** @var array */
    private $localBranches = [];

    /** @var array */
    private $remoteBranches = [];

    public function init($repoPath)
    {
        $this->gitRepo = new GitRepositoryHelper($repoPath);
        $this->localBranches = [];
        $this->remoteBranches = [];
    }

    public function getBranches($repoPath)
    {
        $this->init($repoPath);
        $branches = $this->gitRepo->getBranches();

        foreach ($branches as $branch) {
            if (substr($branch, 0,15) == 'remotes/origin/') {
                $this->remoteBranches[] = str_replace('remotes/origin/','',$branch);
                continue;
            }

            if (substr($branch, 0, 17) == 'remotes/composer/') {
                continue;
            }

            $this->localBranches[] = $branch;
        }

        return $this->localBranches;
    }

    public function hasRemote($branch)
    {
        return in_array($branch, $this->remoteBranches);
    }

    public function deleteBranch($branch)
    {
        if (!is_object($this->gitRepo)) {
            return;
        }

        $this->gitRepo->removeBranch($branch);
    }
}