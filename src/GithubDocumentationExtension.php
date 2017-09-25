<?php namespace Anomaly\GithubDocumentationExtension;

use Anomaly\ConfigurationModule\Configuration\Form\ConfigurationFormBuilder;
use Anomaly\DocumentationModule\Documentation\DocumentationExtension;
use Anomaly\GithubDocumentationExtension\Command\GetComposer;
use Anomaly\GithubDocumentationExtension\Command\GetStructure;

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
     * @param $reference
     * @return array
     */
    public function build($reference)
    {
        return $this->dispatch(new GetStructure($this, $reference));
    }

    /**
     * Return the composer json object.
     *
     * @param $reference
     * @return \stdClass
     */
    public function composer($reference)
    {
        return $this->dispatch(new GetComposer($this, $reference));
    }

    /**
     * Validate the configuration.
     *
     * @param ConfigurationFormBuilder $builder
     * @return bool
     */
    public function validate(ConfigurationFormBuilder $builder)
    {
        return $this->dispatch(new ValidateConfiguration($this, $reference));
    }
}
