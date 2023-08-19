<?php

declare(strict_types=1);

namespace GrumphpDrupalCheck;

use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Config\ConfigOptionsResolver;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Drupal check task.
 */
final class DrupalCheck extends AbstractExternalTask
{

  public static function getConfigurableOptions(): ConfigOptionsResolver {
    $resolver = new OptionsResolver();
    $resolver->setDefaults([
      'drupal_root' => '',
      'memory_limit' => '',
      'deprecations' => true,
      'analysis' => false,
      'style' => false,
      'php8' => false,
      'exclude_dir' => [],
    ]);
    $resolver->addAllowedTypes('drupal_root', ['string', 'null']);
    $resolver->addAllowedTypes('memory_limit', ['string', 'null']);
    $resolver->addAllowedTypes('deprecations', ['boolean']);
    $resolver->addAllowedTypes('analysis', ['boolean']);
    $resolver->addAllowedTypes('style', ['boolean']);
    $resolver->addAllowedTypes('php8', ['boolean']);
    $resolver->addAllowedTypes('exclude_dir', ['array']);

    return ConfigOptionsResolver::fromOptionsResolver($resolver);
  }

  public function canRunInContext(ContextInterface $context): bool
  {
      return $context instanceof GitPreCommitContext || $context instanceof RunContext;
  }

  public function run(ContextInterface $context): TaskResultInterface
  {
    $config = $this->getConfig();
    $options = $config->getOptions();
    $files = $context->getFiles();
    $triggered_by = [
      'php',
      'inc',
      'module',
      'install',
      'profile',
      'theme',
    ];
    $files = $files->extensions($triggered_by);

    if (0 === count($files)) {
        return TaskResult::createSkipped($this, $context);
    }

    $arguments = $this->processBuilder->createArgumentsForCommand('drupal-check');
    !$options['analysis'] ?: $arguments->add('--analysis');
    !$options['deprecations'] ?: $arguments->add('--deprecations');
    !$options['style'] ?: $arguments->add('--style');
    !$options['php8'] ?: $arguments->add('--php8');
    $arguments->add('--no-progress');
    $arguments->addOptionalArgument('--drupal-root=%s', $options['drupal_root']);
    $arguments->addOptionalArgument('--memory-limit=%s', $options['memory_limit']);
    $arguments->addOptionalCommaSeparatedArgument('--exclude-dir=%s', $options['exclude_dir']);
    $arguments->addFiles($files);
    $process = $this->processBuilder->buildProcess($arguments);
    $process->run();

    if (!$process->isSuccessful()) {
        $output = $this->formatter->format($process);
        return TaskResult::createFailed($this, $context, $output);
    }

    return TaskResult::createPassed($this, $context);
  }

}
