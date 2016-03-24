<?php namespace Anomaly\GithubDocumentationExtension;

use Anomaly\Streams\Platform\Addon\Extension\Extension;

/**
 * Class GithubDocumentationExtension
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\GithubDocumentationExtension
 */
class GithubDocumentationExtension extends Extension
{

    /**
     * This extension a documentation source
     * for projects hosted on Github.
     *
     * @var null|string
     */
    protected $provides = 'anomaly.module.documentation::source.github';

}
