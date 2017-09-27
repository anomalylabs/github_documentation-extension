<?php namespace Anomaly\GithubDocumentationExtension\Command;

use Anomaly\ConfigurationModule\Configuration\Contract\ConfigurationRepositoryInterface;
use Anomaly\DocumentationModule\Documentation\DocumentationExtension;
use Anomaly\DocumentationModule\Documentation\DocumentationParser;
use Anomaly\DocumentationModule\Project\Contract\ProjectInterface;
use Github\Client;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class GetPages
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class GetPages
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
     * Create a new GetPages instance.
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
     * @param DocumentationParser              $parser
     * @param Repository                       $config
     * @return array
     */
    public function handle(
        ConfigurationRepositoryInterface $configuration,
        DocumentationParser $parser,
        Repository $config
    ) {
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

        $content = $client
            ->repos()
            ->contents()
            ->show(
                $username,
                $repository,
                $this->path ?: 'docs',
                $this->reference
            );

        array_map(
            function ($resource) use ($parser, &$pages) {

                if ($resource['type'] == 'dir') {

                    $pages[$parser->name($resource['name'])] = $this->dispatch(
                        new GetPages($this->extension, $this->reference, $resource['path'])
                    );
                }

                if ($resource['type'] != 'dir') {

                    $content = $this->dispatch(
                        new GetContent($this->extension, $this->reference, $resource['path'])
                    );

                    $pages[$parser->name(
                        basename(
                            $resource['name'],
                            '.' . pathinfo($resource['name'], PATHINFO_EXTENSION)
                        )
                    )] = array_merge(
                        ['content' => $parser->content($content)],
                        $parser->attributes($content)
                    );
                }
            },
            $content
        );

        return $pages;
    }
}
