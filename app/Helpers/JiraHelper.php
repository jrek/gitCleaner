<?php

namespace App\Helpers;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

class JiraHelper
{
    /** @var IssueService */
    private $jira;

    /** @var string */
    private $host;

    /** @var string */
    private $user;

    /** @var string */
    private $password;

    /** @var string */
    private $cookiePath = APP_PATH.'/tmp/jira-cookie.txt';

    private $cache = true;

    private $cacheFile = APP_PATH.'/tmp/jira-issues.json';

    public function __construct($host = '', $user = '', $password = '')
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->init();
    }

    public function init()
    {
        $this->deleteCookeFile();
        $this->jira = new IssueService(new ArrayConfiguration([
            'jiraHost' => 'https://jira.etouches.com/',
            // for basic authorization:
            'jiraUser' => 'ykoshelevskyy',
            'jiraPassword' => 'hipchat@et2',
            // to enable session cookie authorization (with basic authorization only)
            'cookieAuthEnabled' => true,
            'cookieFile' => $this->cookiePath,
        ]));
    }

    public function getIssueStatus($issue='', $cookieRemoved = true)
    {
        $status = '';

        if ($this->cache === true && file_exists($this->cacheFile)) {
            $cacheData = file_get_contents($this->cacheFile);
            $data = json_decode($cacheData, true);

            if (is_array($data) && array_key_exists($issue, $data)) {
                return $data[$issue];
            }
        }

        try {
            $queryParam = [
                'fields' => [
                    'summary',
                    'status'
                ],
                'expand' => [
                    'renderedFields',
                    'names',
                    'schema',
                    'transitions',
                    'operations',
                    'editmeta',
                    'changelog',
                ]
            ];

            $issueObj = $this->jira->get($issue, $queryParam);

            $status = $issueObj->fields->status->name.PHP_EOL;
            $status = trim($status);

            if ($this->cache === true) {
                $data = [];
                if (file_exists($this->cacheFile)) {
                    $cacheData = file_get_contents($this->cacheFile);
                    $data = json_decode($cacheData, true);
                }
                $data[$issue] = $status;
                $this->writeData(json_encode($data, JSON_UNESCAPED_UNICODE));
            }

        } catch (JiraException $e) {
            // so far I found 2 types of error codes:
            //  401 - Not Logged In
            //  404 - Not Found
            if ($e->getCode() == 401 && $cookieRemoved === true) {
                $this->init();
                $this->getIssueStatus($issue, false);
            }
        }

        return $status;
    }

    public function deleteCookeFile()
    {
        if (!file_exists($this->cookiePath)) {
            return;
        }
        unlink($this->cookiePath);
    }

    private function writeData($data)
    {
        $fh = fopen($this->cacheFile, 'w');
        fwrite($fh, $data);
        fclose($fh);
    }
}