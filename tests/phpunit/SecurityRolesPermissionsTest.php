<?php

namespace tests\phpunit;

use weitzman\DrupalTestTraits\ExistingSiteBase;
use Drupal\user\Entity\Role;

/**
 * A test to confirm that roles are associated with the correct permissions.
 */
class SecurityRolesPermissions extends ExistingSiteBase {

  /**
   * Test method to deterine role are associated with the expected permissions.
   *
   * @group security
   * @group all
   *
   * @dataProvider expectedPerms
   */
  public function testSecurityRolesPermissions($roleMatch, $expectedPerms) {
    $role = Role::load($roleMatch);
    $permissions = NULL;

    if (isset($role)) {
      $permissions = $role->getPermissions();
      $message = "The permissions for the " . $roleMatch . " do not match the expected permissions: \n '" . implode("',\n '", $permissions) . "'\n";
    }
    else {
      $message = 'The ' . $roleMatch . ' role is missing from the system.';
    }

    // Test assertion.
    $match = ($expectedPerms == $permissions);

    $this->assertTrue($match, $message);
  }

  /**
   * Returns expected roles amd associated permissions.
   *
   * @return array
   *   Array containing all the roles in the system as an array
   */
  public function expectedPerms() {
    return [
      [
        'anonymous',
        [
          'access content',
          'access site-wide contact form',
          'view media',
        ],
      ],
      [
        'authenticated',
        [
          'access content',
          'access environment indicator',
          'access site-wide contact form',
          'access taxonomy overview',
          'execute graphql requests',
          'execute persisted graphql requests',
          'view media',
        ],
      ],
      [
        'content_api_consumer',
        [
          'use graphql explorer',
          'use graphql voyager',
          'view any unpublished content',
          'view own unpublished content',
        ],
      ],
      [
        'content_editor',
        [
          'access administration pages',
          'access content overview',
          'access image_browser entity browser pages',
          'access media overview',
          'access media_browser entity browser pages',
          'access shortcuts',
          'access toolbar',
          'access user profiles',
          'administer menu',
          'create alert block content',
          'create document media',
          'create event content',
          'create health_care_local_facility content',
          'create health_care_local_health_service content',
          'create health_care_region_detail_page content',
          'create image media',
          'create landing_page content',
          'create media',
          'create news_story content',
          'create outreach_asset content',
          'create page content',
          'create person_profile content',
          'create press_release content',
          'create promo block content',
          'create regional_health_care_service_des content',
          'create support_service content',
          'create video media',
          'delete media',
          'edit any document media',
          'edit any documentation_page content',
          'edit any event content',
          'edit any health_care_local_facility content',
          'edit any health_care_local_health_service content',
          'edit any health_care_region_detail_page content',
          'edit any health_care_region_page content',
          'edit any image media',
          'edit any landing_page content',
          'edit any news_story content',
          'edit any office content',
          'edit any outreach_asset content',
          'edit any page content',
          'edit any person_profile content',
          'edit any press_release content',
          'edit any regional_health_care_service_des content',
          'edit any support_service content',
          'edit any video media',
          'edit own event content',
          'edit own health_care_local_facility content',
          'edit own health_care_local_health_service content',
          'edit own health_care_region_detail_page content',
          'edit own health_care_region_page content',
          'edit own landing_page content',
          'edit own news_story content',
          'edit own office content',
          'edit own outreach_asset content',
          'edit own page content',
          'edit own person_profile content',
          'edit own press_release content',
          'edit own regional_health_care_service_des content',
          'edit own support_service content',
          'rebuild tablefield',
          'schedule editorial transition create_new_draft',
          'update any alert block content',
          'update any media',
          'update any promo block content',
          'update media',
          'use editorial transition archived_published',
          'use editorial transition create_new_draft',
          'use editorial transition review',
          'use moderation dashboard',
          'use moderation sidebar',
          'use text format rich_text',
          'use workbench access',
          'view all media revisions',
          'view all revisions',
          'view any unpublished content',
          'view event revisions',
          'view health_care_local_facility revisions',
          'view health_care_local_health_service revisions',
          'view health_care_region_detail_page revisions',
          'view health_care_region_page revisions',
          'view landing_page revisions',
          'view latest version',
          'view news_story revisions',
          'view office revisions',
          'view outreach_asset revisions',
          'view own unpublished content',
          'view page revisions',
          'view person_profile revisions',
          'view press_release revisions',
          'view regional_health_care_service_des revisions',
          'view sections in toolbar',
          'view support_service revisions',
          'view the administration theme',
          'view unpublished paragraphs',
          'view workbench access information',
        ],
      ],
      [
        'content_reviewer',
        [
          'access administration pages',
          'access content overview',
          'access image_browser entity browser pages',
          'access media overview',
          'access media_browser entity browser pages',
          'access shortcuts',
          'access toolbar',
          'access user profiles',
          'administer menu',
          'create alert block content',
          'create document media',
          'create event content',
          'create health_care_local_facility content',
          'create health_care_local_health_service content',
          'create health_care_region_detail_page content',
          'create image media',
          'create landing_page content',
          'create media',
          'create news_story content',
          'create outreach_asset content',
          'create page content',
          'create person_profile content',
          'create press_release content',
          'create promo block content',
          'create regional_health_care_service_des content',
          'create support_service content',
          'create video media',
          'delete media',
          'edit any document media',
          'edit any documentation_page content',
          'edit any event content',
          'edit any health_care_local_facility content',
          'edit any health_care_local_health_service content',
          'edit any health_care_region_detail_page content',
          'edit any health_care_region_page content',
          'edit any image media',
          'edit any landing_page content',
          'edit any news_story content',
          'edit any office content',
          'edit any outreach_asset content',
          'edit any page content',
          'edit any person_profile content',
          'edit any press_release content',
          'edit any regional_health_care_service_des content',
          'edit any support_service content',
          'edit any video media',
          'edit own event content',
          'edit own health_care_local_facility content',
          'edit own health_care_local_health_service content',
          'edit own health_care_region_detail_page content',
          'edit own health_care_region_page content',
          'edit own landing_page content',
          'edit own news_story content',
          'edit own office content',
          'edit own outreach_asset content',
          'edit own page content',
          'edit own person_profile content',
          'edit own press_release content',
          'edit own regional_health_care_service_des content',
          'edit own support_service content',
          'rebuild tablefield',
          'schedule editorial transition create_new_draft',
          'update any alert block content',
          'update any media',
          'update any promo block content',
          'update media',
          'use editorial transition archived_published',
          'use editorial transition create_new_draft',
          'use editorial transition review',
          'use editorial transition stage_for_publishing',
          'use moderation dashboard',
          'use moderation sidebar',
          'use text format rich_text',
          'use workbench access',
          'view all media revisions',
          'view all revisions',
          'view any unpublished content',
          'view event revisions',
          'view health_care_local_facility revisions',
          'view health_care_local_health_service revisions',
          'view health_care_region_detail_page revisions',
          'view health_care_region_page revisions',
          'view landing_page revisions',
          'view latest version',
          'view news_story revisions',
          'view office revisions',
          'view outreach_asset revisions',
          'view own unpublished content',
          'view page revisions',
          'view person_profile revisions',
          'view press_release revisions',
          'view regional_health_care_service_des revisions',
          'view sections in toolbar',
          'view support_service revisions',
          'view the administration theme',
          'view unpublished paragraphs',
          'view workbench access information',
        ],
      ],
      [
        'content_publisher',
        [
          'access administration pages',
          'access content overview',
          'access image_browser entity browser pages',
          'access media overview',
          'access media_browser entity browser pages',
          'access shortcuts',
          'access toolbar',
          'access user profiles',
          'administer menu',
          'create alert block content',
          'create document media',
          'create event content',
          'create health_care_local_facility content',
          'create health_care_local_health_service content',
          'create health_care_region_detail_page content',
          'create health_care_region_page content',
          'create image media',
          'create landing_page content',
          'create media',
          'create news_story content',
          'create office content',
          'create outreach_asset content',
          'create page content',
          'create person_profile content',
          'create press_release content',
          'create promo block content',
          'create regional_health_care_service_des content',
          'create support_service content',
          'create terms in health_care_service_taxonomy',
          'create url aliases',
          'create video media',
          'delete any alert block content',
          'delete any document media',
          'delete any event content',
          'delete any health_care_local_facility content',
          'delete any health_care_local_health_service content',
          'delete any health_care_region_detail_page content',
          'delete any health_care_region_page content',
          'delete any image media',
          'delete any landing_page content',
          'delete any media',
          'delete any news_story content',
          'delete any office content',
          'delete any outreach_asset content',
          'delete any page content',
          'delete any person_profile content',
          'delete any press_release content',
          'delete any promo block content',
          'delete any regional_health_care_service_des content',
          'delete any support_service content',
          'delete any video media',
          'delete media',
          'delete own document media',
          'delete own event content',
          'delete own health_care_local_facility content',
          'delete own health_care_local_health_service content',
          'delete own health_care_region_detail_page content',
          'delete own health_care_region_page content',
          'delete own image media',
          'delete own landing_page content',
          'delete own news_story content',
          'delete own office content',
          'delete own outreach_asset content',
          'delete own page content',
          'delete own person_profile content',
          'delete own press_release content',
          'delete own regional_health_care_service_des content',
          'delete own support_service content',
          'delete own video media',
          'delete terms in health_care_service_taxonomy',
          'edit any document media',
          'edit any documentation_page content',
          'edit any event content',
          'edit any health_care_local_facility content',
          'edit any health_care_local_health_service content',
          'edit any health_care_region_detail_page content',
          'edit any health_care_region_page content',
          'edit any image media',
          'edit any landing_page content',
          'edit any news_story content',
          'edit any office content',
          'edit any outreach_asset content',
          'edit any page content',
          'edit any person_profile content',
          'edit any press_release content',
          'edit any regional_health_care_service_des content',
          'edit any support_service content',
          'edit any video media',
          'edit own document media',
          'edit own event content',
          'edit own health_care_local_facility content',
          'edit own health_care_local_health_service content',
          'edit own health_care_region_detail_page content',
          'edit own health_care_region_page content',
          'edit own image media',
          'edit own landing_page content',
          'edit own news_story content',
          'edit own office content',
          'edit own outreach_asset content',
          'edit own page content',
          'edit own person_profile content',
          'edit own press_release content',
          'edit own regional_health_care_service_des content',
          'edit own support_service content',
          'edit own video media',
          'edit terms in health_care_service_taxonomy',
          'rebuild tablefield',
          'revert all revisions',
          'revert event revisions',
          'revert health_care_local_facility revisions',
          'revert health_care_local_health_service revisions',
          'revert health_care_region_detail_page revisions',
          'revert health_care_region_page revisions',
          'revert landing_page revisions',
          'revert news_story revisions',
          'revert office revisions',
          'revert outreach_asset revisions',
          'revert page revisions',
          'revert person_profile revisions',
          'revert press_release revisions',
          'revert regional_health_care_service_des revisions',
          'revert support_service revisions',
          'schedule editorial transition archive',
          'schedule editorial transition archived_published',
          'schedule editorial transition create_new_draft',
          'schedule editorial transition publish',
          'update any alert block content',
          'update any media',
          'update any promo block content',
          'update media',
          'use editorial transition archive',
          'use editorial transition archived_published',
          'use editorial transition create_new_draft',
          'use editorial transition publish',
          'use editorial transition review',
          'use editorial transition stage_for_publishing',
          'use moderation dashboard',
          'use moderation sidebar',
          'use text format rich_text',
          'use workbench access',
          'view all media revisions',
          'view all revisions',
          'view any moderation dashboard',
          'view any unpublished content',
          'view event revisions',
          'view health_care_local_facility revisions',
          'view health_care_local_health_service revisions',
          'view health_care_region_detail_page revisions',
          'view health_care_region_page revisions',
          'view landing_page revisions',
          'view latest version',
          'view news_story revisions',
          'view office revisions',
          'view outreach_asset revisions',
          'view own unpublished content',
          'view page revisions',
          'view person_profile revisions',
          'view press_release revisions',
          'view regional_health_care_service_des revisions',
          'view sections in toolbar',
          'view support_service revisions',
          'view the administration theme',
          'view unpublished paragraphs',
          'view workbench access information',
        ],
      ],
      [
        'admnistrator_users',
        [
          'access content overview',
          'access shortcuts',
          'access toolbar',
          'administer menu',
          'administer nodes',
          'administer users',
          'assign content_api_consumer role',
          'assign content_editor role',
          'assign content_publisher role',
          'assign content_reviewer role',
          'assign documentation_editor role',
          'assign selected workbench access',
          'batch update workbench access',
          'bypass password policies',
          'bypass workbench access',
          'create terms in administration',
          'delete terms in administration',
          'edit terms in administration',
          'manage password reset',
          'use moderation dashboard',
          'use text format rich_text',
          'view any moderation dashboard',
          'view sections in toolbar',
          'view workbench access information',
        ],
      ],
    ];
  }

}
