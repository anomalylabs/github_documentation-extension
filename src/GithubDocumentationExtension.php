<?php namespace Anomaly\GithubDocumentationExtension;

use Anomaly\DocumentationModule\Documentation\DocumentationExtension;
use Anomaly\DocumentationModule\Project\Contract\ProjectInterface;
use Anomaly\GithubDocumentationExtension\Command\GetComposer;
use Anomaly\GithubDocumentationExtension\Command\GetContent;
use Anomaly\GithubDocumentationExtension\Command\GetStructure;
use Anomaly\Streams\Platform\Addon\Extension\Extension;

/**
 * Class GithubDocumentationExtension
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\GithubDocumentationExtension
 */
class GithubDocumentationExtension extends DocumentationExtension
{

    /**
     * This extension a documentation documentation
     * for projects hosted on Github.
     *
     * @var null|string
     */
    protected $provides = 'anomaly.module.documentation::documentation.github';

    /**
     * Return the documentation structure object.
     *
     * @param ProjectInterface $project
     * @param                  $version
     * @return \stdClass
     */
    public function structure(ProjectInterface $project, $version)
    {
        return $this->dispatch(new GetStructure($project, $version));
    }

    /**
     * Return the composer json object.
     *
     * @param ProjectInterface $project
     * @param                  $version
     * @return \stdClass
     */
    public function composer(ProjectInterface $project, $version)
    {
        return $this->dispatch(new GetComposer($project, $version));
    }

    /**
     * Return the file content for a project.
     *
     * @param ProjectInterface $project
     * @param                  $version
     * @param                  $file
     * @return string
     */
    public function content(ProjectInterface $project, $version, $file)
    {
        return $this->dispatch(new GetContent($project, $version, $file));
    }
}
