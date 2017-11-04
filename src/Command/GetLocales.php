<?php namespace Anomaly\GithubDocumentationExtension\Command;

use Anomaly\ConfigurationModule\Configuration\Contract\ConfigurationRepositoryInterface;
use Anomaly\DocumentationModule\Documentation\DocumentationExtension;
use Anomaly\DocumentationModule\Project\Contract\ProjectInterface;
use Github\Client;
use Illuminate\Config\Repository;

/**
 * Class GetLocales
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class GetLocales
{

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
     * @param Repository                       $config
     * @return array
     */
    public function handle(ConfigurationRepositoryInterface $configuration, Repository $config)
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

        $content = cache()->remember(
            $this->path,
            10,
            function () use ($client, $username, $repository) {
                return $client
                    ->repos()
                    ->contents()
                    ->show(
                        $username,
                        $repository,
                        $this->path ?: 'docs',
                        $this->reference
                    );
            }
        );

        return array_map(
            function ($resource) {
                return $resource['name'];
            },
            array_filter(
                $content,
                function ($resource) {
                    return $resource['type'] == 'dir';
                }
            )
        );
    }
}
