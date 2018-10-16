<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SettingsController extends BaseController
{
    public function indexAction()
    {
        $jira = $this->configHelper->get('jira');
        $repos = $this->configHelper->get('gitRepos');
        $this->render('settings/index.twig', compact('jira', 'repos'));
    }

    public function saveAction()
    {
        $repoNames = $this->request->request->get('repoNames');
        $repoPaths = $this->request->request->get('repoPaths');

        $gitRepos = [];
        if (!empty($repoNames) && !empty($repoPaths)) {
            foreach ($repoNames as $key => $repoName) {
                $gitRepos[ $repoName ] = $repoPaths[$key];
            }
        }

        $this->configHelper->set('jira', $this->request->request->get('jira'));
        $this->configHelper->set('gitRepos', $gitRepos);
        $this->configHelper->save();

        $this->redirect('/settings');
    }
}