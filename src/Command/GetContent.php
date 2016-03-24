<?php namespace Anomaly\GithubDocumentationExtension\Command;

use Anomaly\ConfigurationModule\Configuration\Contract\ConfigurationRepositoryInterface;
use Anomaly\DocumentationModule\Project\Contract\ProjectInterface;
use GrahamCampbell\GitHub\GitHubManager;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class GetContent
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\GithubDocumentationExtension\Command
 */
class GetContent implements SelfHandling
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
     * The file path.
     *
     * @var string
     */
    protected $file;

    /**
     * Create a new GetContent instance.
     *
     * @param ProjectInterface $project
     * @param string           $version
     * @param string           $file
     */
    public function __construct(ProjectInterface $project, $version, $file)
    {
        $this->project = $project;
        $this->version = $version;
        $this->file    = $file;
    }

    /**
     * @param GitHubManager                    $github
     * @param Repository                       $config
     * @param ConfigurationRepositoryInterface $configuration
     * @return string
     */
    public function handle(GitHubManager $github, Repository $config, ConfigurationRepositoryInterface $configuration)
    {
        $this->dispatch(new SetConnection($this->project));

        $namespace = 'anomaly.extension.github_documentation';

        $username   = $configuration->value($namespace . '::username', $this->project->getSlug());
        $repository = $configuration->value($namespace . '::repository', $this->project->getSlug());

        $path = 'docs/' . $config->get('app.locale') . '/' . $this->file . '.md';

        if (!$github->repo()->contents()->exists($username, $repository, $path, $this->version)) {
            $path = 'docs/' . $config->get('app.fallback_locale') . '/' . $this->file . '.md';
        }

        return base64_decode(
            array_get($github->repo()->contents()->show($username, $repository, $path, $this->version), 'content')
        );
    }
}
