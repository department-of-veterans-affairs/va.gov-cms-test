<?php

namespace Drupal\va_gov_backend\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Menu\MenuLinkManager;
use Drupal\Core\Menu\MenuLinkTree;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\va_gov_lovell\LovellOps;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a 'SidebarMenus' block plugin.
 *
 * @Block(
 *   id = "sidebar_menu_block",
 *   admin_label = @Translation("Sidebar Menus block"),
 *   category = @Translation("Menus")
 * )
 */
class SidebarMenusBlock extends BlockBase implements ContainerFactoryPluginInterface {


  /**
   * The alias manager interface.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The route match interface.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The Menu link tree.
   *
   * @var \Drupal\Core\Menu\MenuLinkTree
   */
  protected $menuLinkTree;

  /**
   * The http request.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The menu link manager.
   *
   * @var \Drupal\Core\Menu\MenuLinkManager
   */
  protected $menuLinkManager;

  /**
   * The name of the menu in play.
   *
   * @var string
   */
  protected $menuId = '';

  /**
   * Constructor.
   *
   * @param array $configuration
   *   Array of configuration settings.
   * @param string $plugin_id
   *   The plugin id.
   * @param string $plugin_definition
   *   The plugin definition.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   Core path alias manager.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   Provides an interface for classes representing the result of routing.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Provides an interface for entity type managers.
   * @param \Drupal\Core\Menu\MenuLinkTree $menu_link_tree
   *   Provides access to menu trees.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The http request.
   * @param \Drupal\Core\Menu\MenuLinkManager $menu_link_manager
   *   The menu link manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AliasManagerInterface $alias_manager,
    RouteMatchInterface $route_match,
    EntityTypeManagerInterface $entity_type_manager,
    MenuLinkTree $menu_link_tree,
    RequestStack $request_stack,
    MenuLinkManager $menu_link_manager,
    RendererInterface $renderer
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->aliasManager = $alias_manager;
    $this->routeMatch = $route_match;
    $this->entityTypeManager = $entity_type_manager;
    $this->menuLinkTree = $menu_link_tree;
    $this->requestStack = $request_stack;
    $this->menuLinkManager = $menu_link_manager;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('path_alias.manager'),
      $container->get('current_route_match'),
      $container->get('entity_type.manager'),
      $container->get('menu.link_tree'),
      $container->get('request_stack'),
      $container->get('plugin.manager.menu.link'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $node = $this->routeMatch->getParameters()->get('node');
    $menu_name = $this->getMenuId();
    // Before we start doing stuff, make sure we have a node object.
    if ($node instanceof NodeInterface && !empty($menu_name)) {
      $route_params = ['node' => $node->id()];
      // Load the menu.
      $menu_links = $this->menuLinkManager->loadLinksByRoute('entity.node.canonical', $route_params, $menu_name);
      $lovell_type = LovellOps::getLovellType($node);
      // If it has links, put them together.
      $build = $this->getMenuBuild($menu_links, $lovell_type);
    }

    return [
      '#markup' => $this->renderer->render($build),
    ];
  }

  /**
   * Returns the id of the vamc menu for the current context.
   *
   * @return string
   *   The menu name.
   */
  public function getMenuId() {
    if (empty($this->menuId)) {
      // Has not been set, so create it.
      $alias = $this->requestStack->getCurrentRequest()->getPathInfo();
      $alias_parts = explode('/', $alias);

      // Load the node for the path root.
      $root_alias = '/' . $alias_parts[1];
      $system_path = $this->aliasManager->getPathByAlias($root_alias);
      if (preg_match('/node\/(\d+)/', $system_path, $matches)) {
        $node_storage = $this->entityTypeManager->getStorage('node');
        /** @var \Drupal\node\NodeInterface $node */
        $node = $node_storage->load($matches[1]);
        if (($node instanceof NodeInterface) && ($node->hasField('field_system_menu'))) {
          $this->menuId = $node->field_system_menu->target_id;
        }
      }
      else {
        // The proper node was not found, so attempt a default.
        $this->menuId = LovellOps::getLovellMenuFallback($system_path, $this->menuId);
      }
    }

    return $this->menuId;
  }

  /**
   * Returns the rendered VAMC menu for the current context.
   *
   * @param \Drupal\Core\Menu\MenuLinkInterface[] $menu_links
   *   The links to use in the menu.
   * @param string $lovell_type
   *   The type of Lovell menu (both, tricare, VA, '').
   *
   * @return array
   *   Returns the rendered menu array, or empty array if no menu.
   */
  protected function getMenuBuild(array $menu_links, $lovell_type) {
    if (empty($menu_links)) {
      return [];
    }
    $menu_parameters = new MenuTreeParameters();
    // If we don't do this, we won't see the whole menu on nested pages.
    $menu_parameters->setRoot('');
    $menu_parameters->onlyEnabledLinks();
    $tree = $this->menuLinkTree->load($this->getMenuId(), $menu_parameters);
    $manipulators = [
        // This is needed to sort by weights set in ui.
        ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    if (!empty($lovell_type)) {
      $manipulators[] = [
        'callable' => 'va_gov_lovell.lovellmenulinktreemanipulators:reduceLovellMenu',
        'args' => [$lovell_type],
      ];
    }
    $tree = $this->menuLinkTree->transform($tree, $manipulators);

    return $this->menuLinkTree->build($tree);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
