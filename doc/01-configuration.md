# Configuration Instructions

Before run project you need configure this one. Copy/paste the file googleBigQueryLogger.ini.dist in a folder config at your project root, Remove dist extension.

## Variable structure
| NAME                  | Type         | DESCRIPTION           |
| --------------------- | ------------ | --------------------- |
| environment           | String       | Define current environment for project |
| exclude_environment[] | Array        | Logs created from a list environment are not saved in database. By default this list contains test/debug environment |
| show_results          | String       | Send error messages to the defined error handling routines. This option be used in test/debug env for verify the smooth running of the handler |
| dataset               | String       | Name of BigQuery dataset used to writing logs |
| keyFilePath           | String       | Project use a Service Account Key File, to connect to GCP services. For more informations, for create credentials file read a BigQuery documentation[^1] |

[^1]: [https://cloud.google.com/bigquery/docs/authentication/service-account-file](https://cloud.google.com/bigquery/docs/authentication/service-account-file)
