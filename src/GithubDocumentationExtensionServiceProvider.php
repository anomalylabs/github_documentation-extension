<?php namespace Anomaly\GithubDocumentationExtension;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;

/**
 * Class GithubDocumentationExtensionServiceProvider
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\GithubDocumentationExtension
 */
class GithubDocumentationExtensionServiceProvider extends AddonServiceProvider
{

    /**
     * Additional service providers.
     *
     * @var array
     */
    protected $providers = [
        'GrahamCampbell\GitHub\GitHubServiceProvider'
    ];
}
