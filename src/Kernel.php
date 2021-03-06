<?php

namespace SoureCode\ConventionalChangelog;

use Closure;
use function dirname;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symplify\GitWrapper\GitWrapper;

class Kernel
{
    protected ?ContainerInterface $container = null;

    protected string $environment;

    protected bool $debug;

    protected bool $booted = false;

    public function __construct(string $environment, bool $debug)
    {
        $this->environment = $environment;
        $this->debug = $debug;
    }

    public function boot(): void
    {
        if ($this->debug && !isset($_ENV['SHELL_VERBOSITY']) && !isset($_SERVER['SHELL_VERBOSITY'])) {
            putenv('SHELL_VERBOSITY=3');
            $_ENV['SHELL_VERBOSITY'] = 3;
            $_SERVER['SHELL_VERBOSITY'] = 3;
        }

        if (null === $this->container) {
            $this->initializeContainer();
        }

        $this->booted = true;
    }

    public function initializeContainer(): void
    {
        $containerBuilder = $this->buildContainer();
        $containerBuilder->compile();

        $this->container = $containerBuilder;
    }

    protected function buildContainer(): ContainerBuilder
    {
        $container = $this->getContainerBuilder();
        $loader = $this->getContainerLoader($container);

        $this->registerContainerConfiguration($loader);

        $container->addCompilerPass(new AddConsoleCommandPass());

        return $container;
    }

    private function getContainerBuilder(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->getParameterBag()->add($this->getKernelParameters());

        return $container;
    }

    private function getKernelParameters(): array
    {
        $homeDirectory = getenv('HOME');

        if (!$homeDirectory) {
            throw new RuntimeException('Could not get home directory from env.');
        }

        return [
            'kernel.project_directory' => dirname(__DIR__),
            'kernel.environment' => $this->environment,
            'kernel.debug' => $this->debug,
            'kernel.home_directory' => realpath($homeDirectory),
            'kernel.working_directory' => realpath(getcwd()),
        ];
    }

    private function getContainerLoader(ContainerBuilder $container): DelegatingLoader
    {
        $env = $this->getEnvironment();
        $locator = new FileLocator();
        $resolver = new LoaderResolver(
            [
                new PhpFileLoader($container, $locator),
                new GlobFileLoader($container, $locator),
                new DirectoryLoader($container, $locator),
                new ClosureLoader($container),
            ]
        );

        return new DelegatingLoader($resolver);
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    protected function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(
            function (ContainerBuilder $container) use ($loader) {
                /**
                 * @var PhpFileLoader $kernelLoader
                 */
                $kernelLoader = $loader->getResolver()->resolve(__FILE__);
                $kernelLoader->setCurrentDir(__DIR__);
                /**
                 * @var array $instanceof
                 * @psalm-suppress PossiblyInvalidFunctionCall
                 * @psalm-suppress UndefinedThisPropertyFetch
                 */
                $instanceof = &Closure::bind(
                    function &() {
                        return $this->instanceof;
                    },
                    $kernelLoader,
                    $kernelLoader
                )();

                $configurator = new ContainerConfigurator($container, $kernelLoader, $instanceof, __FILE__, __FILE__);

                $this->configureContainer($configurator, $loader);
            }
        );
    }

    protected function configureContainer(ContainerConfigurator $configurator, LoaderInterface $loader): void
    {
        $path = dirname(__DIR__).'/config/services.php';

        if (is_file($path)) {
            (require $path)($configurator->withPath($path), $this);
        }
    }

    public function shutdown(): void
    {
        if (false === $this->booted) {
            return;
        }

        $this->booted = false;

        $this->container = null;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function getContainer(): ContainerInterface
    {
        if (!$this->container) {
            throw new RuntimeException('Kernel not booted.');
        }

        return $this->container;
    }
}
