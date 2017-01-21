<?php
require_once 'prezire/Config.php';
require_once 'prezire/MySqlBackupManager.php';
final class Client
{
  public function __construct()
  {
    $c = new \prezire\Config();
    date_default_timezone_set($c->timezone);
    $s = new \prezire\MySqlBackupManager($c->appUsername, $c->appPassword);
    $s->setGitExecPath(realpath($c->gitExecPath));
    $s->setMySqlDumpExecPath(realpath($c->mySqlDumpExecPath));
    $s->setDbProps
    (
      $c->dbHostName,
      $c->dbName,
      $c->dbUsername,
      $c->dbPassword
    );
    $s->setTableUniqueColIdent($c->tblUniqueColIdent);
    $s->setProjDir(realpath($c->projDir));
    $s->setTableNames($c->tableNames);
    $s->backup($c->intervalType);
    $s->pushRepo($c->repoBranch);
    //TODO: SSH for Git and Cron.
  }
}
new Client();