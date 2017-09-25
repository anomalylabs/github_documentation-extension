<?php namespace Anomaly\GithubDocumentationExtension\Command;

use Anomaly\ConfigurationModule\Configuration\Contract\ConfigurationRepositoryInterface;
use Anomaly\DocumentationModule\Documentation\DocumentationExtension;
use Github\Client;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\Yaml\Yaml;

/**
 * Class GetPage
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class GetPage
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
     * The documentation path.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new GetPage instance.
     *
     * @param DocumentationExtension $extension
     * @param string                 $reference
     * @param string                 $path
     */
    public function __construct(DocumentationExtension $extension, $reference, $path)
    {
        $this->extension = $extension;
        $this->reference = $reference;
        $this->path      = $path;
    }

    /**
     * Handle the command.
     *
     * @param Repository                       $config
     * @param ConfigurationRepositoryInterface $configuration
     * @return string
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

        $path = $this->path . '/page.yaml';

        if (!$client->repos()->contents()->exists($username, $repository, $path, $this->reference)) {
            return null;
        }

        return (new Yaml())->parse(
            base64_decode(
                array_get(
                    $client
                        ->repos()
                        ->contents()
                        ->show(
                            $username,
                            $repository,
                            $path,
                            $this->reference
                        ),
                    'content'
                )
            )
        );
    }
}
