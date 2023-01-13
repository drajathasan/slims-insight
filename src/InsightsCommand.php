<?php

declare(strict_types=1);

namespace Drajat\SLiMSInsight;

use SLiMS\Cli\Command;
use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;
use NunoMaduro\PhpInsights\Application\Console\Definitions\AnalyseDefinition;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Exceptions\InvalidConfiguration;
use NunoMaduro\PhpInsights\Domain\Kernel;
use NunoMaduro\PhpInsights\Domain\Reflection;
use RuntimeException;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

/**
 * @internal
 */
final class InsightsCommand extends Command
{
    private const FUNDING_MESSAGES = [
        '  - Star or contribute to PHP Insights:',
        '    <options=bold>https://github.com/nunomaduro/phpinsights</>',
        '  - Sponsor the maintainers:',
        '    <options=bold>https://github.com/sponsors/nunomaduro</>',
    ];

    protected string $signature = 'insights';

    protected string $description = 'Analyze the code quality';

    public function handle(): int
    {
        Kernel::bootstrap();

        $configPath = ConfigResolver::resolvePath($this->input);

        if (! file_exists($configPath)) {
            $this->error('First, publish the configuration using: php artisan vendor:publish');
            return 1;
        }

        $configuration = require $configPath;
        $configuration['fix'] = $this->option('fix') && (bool) $this->option('fix') === true;
        try {
            $configuration = ConfigResolver::resolve($configuration, $this->input);
        } catch (InvalidConfiguration $exception) {
            $this->output->writeln([
                '',
                '  <bg=red;options=bold> Invalid configuration </>',
                '    <fg=red>•</> <options=bold>' . $exception->getMessage() . '</>',
                '',
            ]);
            return 1;
        }

        $container = Container::make();
        if (! $container instanceof \League\Container\Container) {
            throw new RuntimeException('Container should be League Container instance');
        }

        $configurationDefinition = $container->extend(Configuration::class);
        $configurationDefinition->setConcrete($configuration);

        $analyseCommand = $container->get(AnalyseCommand::class);
        $output = $this->output;

        $result = $analyseCommand->__invoke($this->input, $output);

        dd($result);

        if ($output instanceof ConsoleOutputInterface) {
            // foreach (self::FUNDING_MESSAGES as $message) {
            //     $output->getErrorOutput()->writeln($message);
            // }
        }

        return 1;
    }

    public function configure(): void
    {
        parent::configure();

        $this->setDefinition(
            AnalyseDefinition::get()
        );

        $this->getDefinition()
            ->getOption('config-path')
            ->setDefault('config/insights.php');
    }
}