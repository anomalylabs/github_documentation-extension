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
     * @param                  $reference
     * @return \stdClass
     */
    public function structure(ProjectInterface $project, $reference)
    {
        return $this->dispatch(new GetStructure($project, $reference));
    }

    /**
     * Return the composer json object.
     *
     * @param ProjectInterface $project
     * @param                  $reference
     * @return \stdClass
     */
    public function composer(ProjectInterface $project, $reference)
    {
        return $this->dispatch(new GetComposer($project, $reference));
    }

    /**
     * Return the file content for a project.
     *
     * @param ProjectInterface $project
     * @param                  $reference
     * @param                  $file
     * @return string
     */
    public function content(ProjectInterface $project, $reference, $file)
    {
        return $this->dispatch(new GetContent($project, $reference, $file));
    }
}
