<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\ConfigHelper;
use App\Helpers\GitHelper;
use App\Helpers\JiraHelper;
use App\Services\SharedService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class IndexController extends BaseController
{
    /** @var GitHelper */
    private $gitHelper;

    /** @var JiraHelper */
    private $jiraHelper;

    public function __construct(
        SharedService $service,
        Request $request,
        Response $response,
        Twig_Environment $twig,
        ConfigHelper $configHelper
    ) {
        parent::__construct($service, $request, $response, $twig, $configHelper);

        $this->gitHelper = new GitHelper();
        $this->jiraHelper = $service->get('JiraHelper');
    }

    public function indexAction()
    {
        $repos = $this->configHelper->get('gitRepos');

        $selectedRepo = $this->request->request->get('selectedRepo');

        if(empty($selectedRepo) && !empty($repos)) {
            $keys = array_keys($repos);
            $repoKey = array_shift( $keys );
            $selectedRepo = $repoKey;
        }
        $selectedRepoPath = $repos[ $selectedRepo ];

        $branchesData = $this->gitHelper->getBranches($selectedRepoPath);

        $branches = [];
        foreach ($branchesData as $branch) {

            $branches[ $branch ]['status'] = false;
            $branches[ $branch ]['hasRemote'] = $this->gitHelper->hasRemote($branch);

            if (strlen(preg_replace("/(ET-[\d]+)/","",$branch)) != 0) {
                continue;
            }

            $branches[$branch]['status'] = $this->jiraHelper->getIssueStatus($branch);

        }

        $this->render('index/index.twig', compact('branches', 'repos', 'selectedRepo'));
    }

    public function deleteAction()
    {
        $repos = $this->configHelper->get('gitRepos');
        $selectedRepo = $this->request->request->get('selectedRepo');

        $this->gitHelper->init($repos[ $selectedRepo ]);

        $branchesToDelete = $this->request->request->get('branches');

        if(empty($branchesToDelete)) {
            $this->redirect('/');
            return;
        }

        try {
            foreach ($branchesToDelete as $branch) {
                $this->gitHelper->deleteBranch($branch);
            }
            $this->redirect('/');
        }catch(\Exception $e) {
            echo $e->getMessage();
        }
    }
}