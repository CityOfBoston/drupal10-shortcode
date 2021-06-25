<?php

namespace Drupal\shortcode_basic_tags\Plugin\Shortcode;

use Drupal\block_content\Entity\BlockContent;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Insert div or span around the text with some css classes.
 *
 * @Shortcode(
 *   id = "block",
 *   title = @Translation("Block"),
 *   description = @Translation("Insert a block.")
 * )
 */
class BlockShortcode extends ShortcodeBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new EntityField.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

    // Merge with default attributes.
    $attributes = $this->getAttributes([
      'id' => '',
      'view' => 'full',
    ],
      $attributes
    );

    if ((int) $attributes['id']) {
      $block_entity = BlockContent::load($attributes['id']);
      if ($block_entity) {
        $block_view = $this->entityTypeManager->getViewBuilder('block_content')->view($block_entity, $attributes['view']);
        if ($block_view) {
          return \Drupal::service('renderer')->render($block_view);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    $output = [];
    $output[] = '<p><strong>' . $this->t('[block id="1" (view="full") /]') . '</strong>';
    $output[] = $this->t('Inserts a block.') . '</p>';
    if ($long) {
      $output[] = '<p>' . $this->t('The block display view can be specified using the <em>view</em> parameter.') . '</p>';
    }

    return implode(' ', $output);
  }

}
