<?php namespace Anomaly\GithubDocumentationExtension\Command;

use Anomaly\ConfigurationModule\Configuration\Contract\ConfigurationRepositoryInterface;
use Anomaly\DocumentationModule\Project\Contract\ProjectInterface;
use Anomaly\EncryptedFieldType\EncryptedFieldTypePresenter;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Config\Repository;

/**
 * Class SetConnection
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\GithubDocumentationExtension\Command
 */
class SetConnection implements SelfHandling
{

    /**
     * The project instance.
     *
     * @var ProjectInterface
     */
    protected $project;

    /**
     * Create a new SetConnection instance.
     *
     * @param ProjectInterface $project
     */
    public function __construct(ProjectInterface $project)
    {
        $this->project = $project;
    }

    /**
     * Handle the connection.
     *
     * @param Repository                       $config
     * @param ConfigurationRepositoryInterface $configuration
     */
    public function handle(Repository $config, ConfigurationRepositoryInterface $configuration)
    {
        $namespace = 'anomaly.extension.github_documentation';

        /* @var EncryptedFieldTypePresenter $token */
        $username   = $configuration->value($namespace . '::username', $this->project->getSlug());
        $repository = $configuration->value($namespace . '::repository', $this->project->getSlug());
        $token      = $configuration->presenter($namespace . '::token', $this->project->getSlug());

        // Decrypt the value.
        $token = $token->decrypt();

        $config->set(
            'github.connections.' . $username . '/' . $repository,
            [
                'token'  => $token,
                'method' => 'token'
            ]
        );
    }
}
