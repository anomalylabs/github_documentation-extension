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
     * The project locale.
     *
     * @var string
     */
    protected $locale;

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
     * @param string           $locale
     * @param null             $path
     */
    public function __construct(DocumentationExtension $extension, $reference, $locale, $path = null)
    {
        $this->extension = $extension;
        $this->reference = $reference;
        $this->locale    = $locale;
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
        $pages = [];

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
            $this->path = 'docs/' . $this->locale;
        }

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
                        $this->path,
                        $this->reference
                    );
            }
        );

        array_map(
            function ($resource) use (&$pages) {

                if ($resource['type'] == 'file') {
                    $pages[] = dirname(
                            str_replace(
                                'docs/' . $this->locale,
                                '',
                                $resource['path']
                            )
                        ) . '/' . basename($resource['name'], '.md');
                }

                if ($resource['type'] == 'dir') {
                    $pages = array_merge(
                        $pages,
                        $this->dispatch(
                            new GetStructure($this->extension, $this->reference, $this->locale, $resource['path'])
                        )
                    );
                }
            },
            $content
        );

        return $pages;
    }
}
