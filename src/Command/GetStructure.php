<?php namespace Anomaly\GithubDocumentationExtension\Command;

use Anomaly\ConfigurationModule\Configuration\Contract\ConfigurationRepositoryInterface;
use Anomaly\DocumentationModule\Project\Contract\ProjectInterface;
use GrahamCampbell\GitHub\GitHubManager;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class GetStructure
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\GithubDocumentationExtension\Command
 */
class GetStructure implements SelfHandling
{

    use DispatchesJobs;

    /**
     * The project instance.
     *
     * @var ProjectInterface
     */
    protected $project;

    /**
     * The project version.
     *
     * @var string
     */
    protected $version;

    /**
     * Create a new GetStructure instance.
     *
     * @param ProjectInterface $project
     * @param string           $version
     */
    public function __construct(ProjectInterface $project, $version)
    {
        $this->project = $project;
        $this->version = $version;
    }

    /**
     * @param GitHubManager                    $github
     * @param Repository                       $config
     * @param ConfigurationRepositoryInterface $configuration
     * @return \stdClass
     */
    public function handle(GitHubManager $github, ConfigurationRepositoryInterface $configuration)
    {
        $this->dispatch(new SetConnection($this->project));

        $namespace = 'anomaly.extension.github_documentation';

        $username   = $configuration->value($namespace . '::username', $this->project->getSlug());
        $repository = $configuration->value($namespace . '::repository', $this->project->getSlug());


        return json_decode(
            base64_decode(
                array_get(
                    $github->repo()->contents()->show(
                        $username,
                        $repository,
                        'docs/structure.json',
                        $this->project->reference($this->version ?: 'master')
                    ),
                    'content'
                )
            )
        );
    }
}
