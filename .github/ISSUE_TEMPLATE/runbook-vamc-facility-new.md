---
name: Runbook - New VAMC Facility
about: changing facility information in the CMS for VAMC facilities
title: 'New VAMC Facility: <insert_name_of_facility>'
labels: Change request, Drupal engineering, Facilities, User support, VA.gov frontend, VAMC
assignees: ''

---

## Intake
- [ ] What triggered this runbook? (Flag in CMS, Help desk ticket, Product team, VHA Digital Media)
Trigger: <insert_trigger>

- [ ] Link to associated help desk ticket (if applicable)
Help desk ticket: <insert_help_desk_link>

- [ ] Name of submitter (if applicable)
Submitter: <insert_name>

- [ ] If the submitter is an editor, send them links to any relevate KB articles for the VAMC Facility product.
KB articles: <insert_kb_article_links>

- [ ] Link to facility in production:
Facility CMS link: <insert_facility_link>
Facility API ID: <insert_facility_API_ID>

## Acceptance criteria

### New VAMC Facility
Please refer to the Knowledge Base article titled "How do I add a facility to my health care system?" for more information: https://prod.cms.va.gov/help/vamc/about-locations-content-for-vamcs/how-do-i-add-a-facility-to-my-health-care-system

#### CMS help desk steps
**Note: If the help desk is waiting on information from the facility staff or editor, add the "Awaiting editor" flag to the facility with a log message that includes a link to this ticket. Remove the flag when the ticket is ready to be worked by the Facilities team. Be sure to preserve the current moderation state of the node when adding or removing the flag.**
- [ ] 1. Become aware that the new facility is now on the Facility API (typically, via a Flag, but this may come in as a helpdesk ticket).
- [ ] 2. If the editor has followed the steps from the above Knowledge Base article and included which section and VAMC the facility belongs to (i.e. VA Pittsburgh), great! **Proceed to step 3.** If not, please check with the editor or VHA digital media regarding what section and VAMC it belongs to.
- [ ] 3. Updates the Section (default is "VAMC facilities", but it should be a VAMC system in a VISN) and VAMC system field accordingly.
- [ ] 4. Communicate with editor (cc VHA Digital Media) to give them go-ahead to complete the content, with this [KB article](https://prod.cms.va.gov/help/vamc/about-locations-content-for-vamcs/how-do-i-add-a-facility-to-my-health-care-system). (See sample notification email below)

<details><summary>Email template </summary>

```

Hello! You should now be able to edit the draft page for this facility, located at [LINK TO NEW FACILITY DRAFT PAGE ON PROD]

Important: Please make sure that all relevant steps listed within the “How do I add a facility to my health care system?” Knowledge Base article have been completed: https://prod.cms.va.gov/help/vamc/about-locations-content-for-vamcs/how-do-i-add-a-facility-to-my-health-care-system

Once finished, please save this page (and all related VAMC Facility Health Service pages) in the moderation state “Draft." Please do not save them as “Published.”

Please let us know when your draft content is complete, so that we can wrap up the technical process from our end before publishing the new facility to VA.gov. Thanks!

```

</details>

- [ ] 5. Create a [URL change request](https://github.com/department-of-veterans-affairs/va.gov-cms/issues/new?assignees=&template=runbook-facility-url-change.md&title=URL+Change+for%3A+%3Cinsert+facility+name%3E), changing the entry from the old facility URL to the new facility URL. (**Note: The URL change request ticket blocks the completion of this ticket.**)

<insert_url_change_request_link>

- [ ] 6. When editor has prepared content and let help desk know, reassign this issue to appropriate CMS engineer on Product Support team, for bulk publishing.

#### CMS engineer steps
- [ ] 7. CMS engineer executes the steps of the URL change request ticket from step 5 above.

(Redirects deploy daily except Friday at 10am ET, or by requesting OOB deploy (of the revproxy job to prod) in #vfs-platform-support. Coordinate the items below and canonical URL change after URL change ticket is merged, deployed, and verified in prod.)

#### Drupal Admin steps (CMS Engineer or Help desk)
_Help desk will complete these steps or escalate to request help from CMS engineering._
- [ ] 8. Drupal Admin bulk publishes nodes and facility.
- [ ] 9. Drupal Admin edit facility node and remove `New facility` flag and save node.
- [ ] 10. Let Help desk know this has been done, if not done by Help desk.


#### CMS Help desk (wrap up)
- [ ] 11. Notify editor and any other stakeholders.
