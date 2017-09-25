<?php namespace Anomaly\GithubDocumentationExtension\Command;

use Anomaly\ConfigurationModule\Configuration\Contract\ConfigurationRepositoryInterface;
use Anomaly\DocumentationModule\Documentation\DocumentationExtension;
use Anomaly\DocumentationModule\Project\Contract\ProjectInterface;
use Github\Client;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class GetStructure
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class GetStructure
{

    use DispatchesJobs;

    /**
     * The documentation extension.
     *
     * @var DocumentationExtension
     */
    protected $extension;

    /**
     * The project reference.
     *
     * @var string
     */
    protected $reference;

    /**
     * The path to get.
     *
     * @var string|null
     */
    protected $path;

    /**
     * Create a new GetStructure instance.
     *
     * @param ProjectInterface $project
     * @param string           $reference
     * @param null             $path
     */
    public function __construct(DocumentationExtension $extension, $reference, $path = null)
    {
        $this->extension = $extension;
        $this->reference = $reference;
        $this->path      = $path;
    }

    /**
     * Handle the command.
     *
     * @param ConfigurationRepositoryInterface $configuration
     * @return \stdClass
     */
    public function handle(Repository $config, ConfigurationRepositoryInterface $configuration)
    {
        $project = $this->extension->getProject();

        $namespace = 'anomaly.extension.github_documentation';

        $token = $config->get($namespace . '::github.token');

        $username = $configuration->value(
            $namespace . '::username',
            $project->getId()
        );

        $repository = $configuration->value(
            $namespace . '::repository',
            $project->getId()
        );

        $client = new Client();

        $client->authenticate($token, null, 'http_token');

        if (!$this->path) {

            $this->path = 'docs/' . $config->get('app.locale');

            if (!$client->repos()->contents()->exists($username, $repository, $this->path, $this->reference)) {
                $this->path = 'docs/' . $config->get('app.fallback_locale');
            }
        }

        $pages = $client
            ->repos()
            ->contents()
            ->show(
                $username,
                $repository,
                $this->path,
                $this->reference
            );

        $pages = array_combine(
            array_map(
                function ($directory) {
                    return $directory['name'];
                },
                $pages
            ),
            array_map(
                function ($page) {

                    if ($page['type'] == 'dir') {

                        $page['children'] = $this->dispatch(
                            new GetStructure($this->extension, $this->reference, $page['path'])
                        );
                    }

                    if ($page['type'] != 'dir') {

                        $page['content'] = $this->dispatch(
                            new GetContent($this->extension, $this->reference, $page['path'])
                        );

                        dd($page);
                    }

                    return $page;
                },
                $pages
            )
        );

        return $pages;
    }
}
