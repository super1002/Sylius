<?php

use Robo\Exception\TaskException;
use Robo\Result;
use Robo\Symfony\ConsoleIO;
use Robo\Tasks;

class RoboFile extends Tasks
{
    const ROOT_DIR = __DIR__;

    const SUCCESS = 'success';

    const FAILED = 'failed';

    const YES = 'yes';

    public function ciPackages(ConsoleIO $io, string $packagesJson): ?Result
    {
        $packages = json_decode($packagesJson, true);
        $result = [];
        $failed = false;

        foreach ($packages as $package) {
            $this->startGroup($package);

            try {
                $processResult = $this->processPackagePipeline($package);
                $result[$package] = null !== $processResult && $processResult->wasSuccessful() ? self::SUCCESS : self::FAILED;
            } catch (TaskException) {
                $result[$package] = self::FAILED;
            }
        }

        $this->endGroup();

        foreach ($result as $packageName => $value) {
            printf("%s %s%s", $value === self::SUCCESS ? '✅' : '❌', $packageName, PHP_EOL);
            $failed = $failed || $value === self::FAILED;
        }

        exit(false === $failed ? 0 : 1);
    }

    /**
     * @throws TaskException
     */
    private function processPackagePipeline(string $package): ?Result
    {
        $symfonyVersion = getenv('SYMFONY_VERSION');
        $useSwiftmailer = getenv('USE_SWIFTMAILER');
        $packagePath = sprintf('%s/src/Sylius/%s', self::ROOT_DIR, $package);

        if (false === $symfonyVersion) {
            throw new RuntimeException('SYMFONY_VERSION environment variable is not set.');
        }

        $task = $this->taskExecStack()
            ->dir($packagePath)
            ->stopOnFail()
            ->exec(sprintf('composer config extra.symfony.require "%s"', $symfonyVersion))
        ;

        if (self::YES === $useSwiftmailer) {
            $task
                ->exec('composer require --no-progress --no-update --no-scripts --no-plugins "sylius/mailer-bundle:^1.8"')
                ->exec('composer require --no-progress --no-update --no-scripts --no-plugins "symfony/swiftmailer-bundle:^3.4"')
            ;
        }

        $task
            ->exec('composer validate --ansi --strict')
            ->exec('composer update --no-scripts --no-interaction')
        ;

        if (in_array($package, ['Bundle/AdminBundle', 'Bundle/ApiBundle', 'Bundle/CoreBundle'])) {
            mkdir(sprintf('%s/test/public/build/admin', $packagePath), 0777, true);
            mkdir(sprintf('%s/test/public/build/shop', $packagePath), 0777, true);
            file_put_contents(sprintf('%s/test/public/build/admin/manifest.json', $packagePath), '{}');
            file_put_contents(sprintf('%s/test/public/build/shop/manifest.json', $packagePath), '{}');
        }

        if ('Bundle/ApiBundle' === $package) {
            $task->exec('test/bin/console doctrine:schema:update --force');
        }

        if (false === str_starts_with($symfonyVersion, '^5.4') && 'Bundle/UserBundle' === $package) {
            $task->exec('rm spec/Security/UserPasswordEncoderSpec.php');
        }

        $task->exec('vendor/bin/phpspec run --ansi --no-interaction -f dot');

        if (file_exists(sprintf('%s/phpunit.xml', $packagePath)) || file_exists(sprintf('%s/phpunit.xml.dist', $packagePath))) {
            $task->exec('vendor/bin/phpunit --colors=always');
        }

        return $task->run();
    }

    private function startGroup(string $groupName): void
    {
        printf("::group::%s\n", $groupName);
    }

    private function endGroup(): void
    {
        echo "\n::endgroup::\n\n";
    }
}
