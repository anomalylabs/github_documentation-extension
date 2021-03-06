<?php namespace Anomaly\GithubDocumentationExtension\Command;

use Anomaly\ConfigurationModule\Configuration\Contract\ConfigurationRepositoryInterface;
use Anomaly\DocumentationModule\Documentation\DocumentationExtension;
use Github\Client;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class GetComposer
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class GetComposer
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
     * Create a new GetComposer instance.
     *
     * @param DocumentationExtension $extension
     * @param string                 $reference
     */
    public function __construct(DocumentationExtension $extension, $reference)
    {
        $this->extension = $extension;
        $this->reference = $reference;
    }

    /**
     * Handle the command.
     *
     * @param Repository                       $config
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

        return json_decode(
            base64_decode(
                array_get(
                    $client
                        ->repos()
                        ->contents()
                        ->show(
                            $username,
                            $repository,
                            'composer.json'
                        ),
                    'content'
                )
            )
        );
    }
}
