<?php namespace Anomaly\GithubDocumentationExtension;

use Anomaly\DocumentationModule\Source\SourceExtension;
use Anomaly\GithubDocumentationExtension\Command\GetFileContent;
use Anomaly\Streams\Platform\Addon\Extension\Extension;

/**
 * Class GithubDocumentationExtension
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\GithubDocumentationExtension
 */
class GithubDocumentationExtension extends SourceExtension
{

    /**
     * This extension a documentation source
     * for projects hosted on Github.
     *
     * @var null|string
     */
    protected $provides = 'anomaly.module.documentation::source.github';

    /**
     * Return the file's content.
     *
     * @param $section
     * @param $file
     * @return string
     */
    public function content($section, $file)
    {
        return $this->dispatch(new GetFileContent($section, $file));
    }

}
