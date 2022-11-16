# Moodle plugin: local_delcoursesuv #

## Motivation for this plugin
Delete Courses UV is a customized Moodle plugin that allows to automatically delete a large number of courses in the platform.

There are two Moodle tasks to perform this:
- The first task allows to enqueue courses to be deleted based on some criterias that are defined in the plugin configuration
- The second task performs the deletion of courses that were enqueued by the first task

Enqueued courses are sent to the <i>local_delcoursesuv_todelete</i> database table. Once deleted, they are removed from that table and sent to <i>local_delcoursesuv_deleted</i> database table to store historical data about the deletion.

This plugin is supported by the plugin https://github.com/desarrolloant/back2restuv, because the latter is the one that backs up and restores courses to the Campus Virtual Historia server, so the already backed up courses can be deleted from the production server.

## Installation
Run the following commands under Moodle root directory:
1. Clone this repository to <i>/path/to/moodle/local/delcoursesuv</i>:
```
git clone https://github.com/desarrolloant/moodle-local_delcoursesuv.git local/delcoursesuv
```
2. Upgrade the Moodle site using Moodle CLI:
```
php admin/cli/upgrade
```

## Configuration
As admin you have to configure the following settings in <i>path/to/moodle/admin/settings.php?section=managelocaldelcoursesuv</i> (you can find more details about each setting in there):
- Deletion criterias:
  - Course creation date
  - Course last modification date
  - Number of course categories to exclude and which of them
- Advanced settings
  - Limit SQL query to enqueue courses
  - Deletion task queue size
- Notification
  - Users to notify about the deletion process
- Client for web service:
  - URL to Campus Virtual Historia
  - Web service authorized user token
  - Web service function name

## Copyright
Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
