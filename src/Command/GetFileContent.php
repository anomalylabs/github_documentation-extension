<?php namespace Anomaly\GithubDocumentationExtension\Command;

use GrahamCampbell\GitHub\GitHubManager;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Config\Repository;

/**
 * Class GetFileContent
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\GithubDocumentationExtension\Command
 */
class GetFileContent implements SelfHandling
{

    /**
     * The documentation section.
     *
     * @var string
     */
    protected $section;

    /**
     * The documentation file.
     *
     * @var string
     */
    protected $file;

    /**
     * Create a new GetFileContent instance.
     *
     * @param $section
     * @param $file
     */
    public function __construct($section, $file)
    {
        $this->section = $section;
        $this->file    = $file;
    }

    public function handle(GitHubManager $github, Repository $config)
    {
        $config->set(
            'github.connections.main',
            [
                'token'  => env('GITHUB_TOKEN'),
                'method' => 'token'
            ]
        );

        return $github->repo()->contents()->show('pyrocms', 'pyrocms', 'docs/setup/installation.md', 'v3.0.0-beta3');
    }
}
