<?php namespace Anomaly\GithubDocumentationExtension\Command;

use Anomaly\ConfigurationModule\Configuration\Contract\ConfigurationRepositoryInterface;
use Anomaly\DocumentationModule\Documentation\DocumentationExtension;
use Anomaly\DocumentationModule\Documentation\DocumentationParser;
use Github\Client;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;

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
     * The project locale.
     *
     * @var string
     */
    protected $locale;

    /**
     * The page path.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new GetPage instance.
     *
     * @param DocumentationExtension $extension
     * @param string                 $reference
     * @param                        $locale
     * @param string                 $path
     */
    public function __construct(DocumentationExtension $extension, $reference, $locale, $path)
    {
        $this->extension = $extension;
        $this->reference = $reference;
        $this->locale    = $locale;
        $this->path      = $path;
    }

    /**
     * Handle the command.
     *
     * @param Repository                       $config
     * @param DocumentationParser              $parser
     * @param ConfigurationRepositoryInterface $configuration
     * @return string
     */
    public function handle(
        Repository $config,
        DocumentationParser $parser,
        ConfigurationRepositoryInterface $configuration
    ) {
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

        $path = 'docs/' . $this->locale . $this->path . '.md';

        $client = new Client();

        $client->authenticate($token, null, 'http_token');

        $page = cache()->remember(
            'content.' . $path,
            10,
            function () use ($path, $client, $username, $repository) {
                return $client
                    ->repos()
                    ->contents()
                    ->show(
                        $username,
                        $repository,
                        $path,
                        $this->reference
                    );
            }
        );

        $content = base64_decode(array_get($page, 'content'));

        $data    = $parser->attributes($content);
        $content = $parser->content($content);

        $data['path'] = $this->path;
        $data['url']  = "https://github.com/{$username}/{$repository}/tree/{$this->reference}/docs/{$this->locale}{$this->path}.md";
        
        return [
            'title'            => array_pull($data, 'title'),
            'meta_title'       => array_pull($data, 'meta_title'),
            'meta_description' => array_pull($data, 'meta_description'),
            'path'             => $parser->path($this->path),
            'content'          => $content,
            'data'             => $data,
        ];
    }
}
