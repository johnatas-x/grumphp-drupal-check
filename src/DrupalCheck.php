<?php

namespace GrumphpDrupalCheck;

use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Drupal check task.
 */
class DrupalCheck extends AbstractExternalTask
{

  /**
   * @param ContextInterface $context
   *
   * @return bool
   */
  public function canRunInContext(ContextInterface $context): bool
  {
      return $context instanceof GitPreCommitContext || $context instanceof RunContext;
  }

  /**
   * {@inheritdoc}
   */
  public static function getConfigurableOptions(): OptionsResolver
  {
      $resolver = new OptionsResolver();
      $resolver->setDefaults([
        'drupal_root' => '',
        'memory_limit' => '',
        'deprecations' => true,
        'analysis' => false,
        'php8' => false,
        'exclude_dir' => [],
      ]);
      $resolver->addAllowedTypes('drupal_root', ['string', 'null']);
      $resolver->addAllowedTypes('memory_limit', ['string', 'null']);
      $resolver->addAllowedTypes('deprecations', ['boolean']);
      $resolver->addAllowedTypes('analysis', ['boolean']);
      $resolver->addAllowedTypes('php8', ['boolean']);
      $resolver->addAllowedTypes('exclude_dir', ['array']);

      return $resolver;
  }

  /**
   * {@inheritdoc}
   */
  public function run(ContextInterface $context): TaskResultInterface
  {
    $config = $this->getConfig();
    $options = $config->getOptions();

    /** @var \GrumPHP\Collection\FilesCollection $files */
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
    if (0 === \count($files)) {
        return TaskResult::createSkipped($this, $context);
    }
    $arguments = $this->processBuilder->createArgumentsForCommand('drupal-check');
    !$options['analysis'] ?: $arguments->add('--analysis');
    !$options['deprecations'] ?: $arguments->add('--deprecations');
    !$options['php8'] ?: $arguments->add('--php8');
    $arguments->add('--no-progress');
    $arguments->addOptionalArgument('--drupal-root=%s', $options['drupal_root']);
    $arguments->addOptionalArgument('--memory-limit=%s', $options['memory_limit']);
    $arguments->addOptionalCommaSeparatedArgument('--exclude-dir=%s', $config['exclude_dir']);
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
