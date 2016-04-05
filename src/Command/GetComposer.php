<?php namespace Anomaly\GithubDocumentationExtension\Command;

use Anomaly\ConfigurationModule\Configuration\Contract\ConfigurationRepositoryInterface;
use Anomaly\DocumentationModule\Project\Contract\ProjectInterface;
use Anomaly\EncryptedFieldType\EncryptedFieldTypePresenter;
use Guzzle\Http\Client;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class GetComposer
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\GithubDocumentationExtension\Command
 */
class GetComposer implements SelfHandling
{

    use DispatchesJobs;

    /**
     * The project instance.
     *
     * @var ProjectInterface
     */
    protected $project;

    /**
     * The project reference.
     *
     * @var string
     */
    protected $reference;

    /**
     * Create a new GetComposer instance.
     *
     * @param ProjectInterface $project
     * @param string           $reference
     */
    public function __construct(ProjectInterface $project, $reference)
    {
        $this->project   = $project;
        $this->reference = $reference;
    }

    /**
     * @param ConfigurationRepositoryInterface $configuration
     * @return \stdClass
     */
    public function handle(ConfigurationRepositoryInterface $configuration)
    {
        $namespace = 'anomaly.extension.github_documentation';

        /* @var EncryptedFieldTypePresenter $token */
        $username   = $configuration->value($namespace . '::username', $this->project->getSlug());
        $repository = $configuration->value($namespace . '::repository', $this->project->getSlug());
        $token      = $configuration->presenter($namespace . '::token', $this->project->getSlug());

        // Decrypt the value.
        $token = $token->decrypt();

        $client = new Client('https://api.github.com');

        dd(json_decode($client->get("repos/{$username}/pyro-theme/contents/composer.json?token={$token}")->send()->getBody(true), true));

        return json_decode(
            base64_decode(
                array_get(
                    $github->connection($username . '/' . $repository)->repo()->contents()->show(
                        $username,
                        $repository,
                        'composer.json',
                        $this->reference
                    ),
                    'content'
                )
            )
        );
    }
}
